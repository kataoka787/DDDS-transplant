<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Utils;
use \Firebase\JWT\JWT;

class Box_Api
{
    public $headers;
    public $accessToken;
    public $response;

    public function __construct()
    {
        $this->accessToken = $this->loadToken();
        $this->headers = array(
            "Authorization" => "Bearer $this->accessToken",
            "Content-Type" => "application/json",
        );
        log_message("info", "Box api called.");
    }

    /**
     * Make http request
     *
     * @param string $method
     * @param string $contentType
     * @param string $url
     * @param array $data
     * @return array $responseDataWithStatusCode
     */
    public function request($method = "POST", $contentType, $url, $data, $isSink = false)
    {
        /* Init request data with header */
        $requestData = array(
            "headers" => array(
                "Authorization" => "Bearer $this->accessToken",
                "Content-Type" => $contentType,
            ),
        );

        /* Set request body depend on http request type */
        switch ($contentType) {
            case CONTENT_TYPE["form-urlencoded"]:
                $requestData["form_params"] = $data;
                break;
            case CONTENT_TYPE["json"]:
                $requestData["json"] = $data;
                break;
            case CONTENT_TYPE["json-patch"]:
                $requestData["json"] = $data;
                break;
            case CONTENT_TYPE["multipart"]:
                /* Default boundary is "&" */
                $requestData["headers"]["Content-Type"] = "multipart/form-data; boundary=&";
                $requestData["body"] = $data;
                break;
        }

        /* Set download path */
        if ($isSink) {
            $stream = Utils::streamFor(fopen("php://memory", 'w'));
            $requestData["sink"] = $stream;
        }

        /* Make request */
        try {
            $client = new Client();
            $res = $client->request($method, $url, $requestData);
            $data = $isSink ? array("content" => $stream->getContents()) : json_decode($res->getBody()->getContents());
            $this->response = array(
                "statusCode" => $res->getStatusCode(),
                "data" => $data,
                "success" => true,
            );
        } catch (RequestException $e) {
            $this->response["success"] = false;
            log_message("error", "Box request error by below request:");
            log_message("error","url: ". $url);
            log_message("error","contentType: ".$contentType);
            log_message("error","data: ". json_encode($data));

            if ($e->hasResponse()) {
                $this->response["statusCode"] = $e->getCode();
                $this->response["data"] = $e->getResponse();
                log_message("error", "Box request error status code: " . $e->getCode());
                log_message("error", "Box request error response message: " . $e->getResponse()->getBody()->getContents());
            }
        }

        return $this->response;
    }

    /**
     * Request access token from Box
     * Also save it to file
     *
     * @return string $accessToken
     */
    public function getToken()
    {
        $requestTime = time();
        /*
         * Prepare access token request params
         * https://developer.box.com/guides/authentication/jwt/without-sdk/
         */
        $key = openssl_pkey_get_private(BOX_CONFIG["private_key"], BOX_CONFIG["passphrase"]);
        $claims = [
            "iss" => BOX_CONFIG["client_id"],
            "sub" => BOX_CONFIG["enterprise_id"],
            "box_sub_type" => "enterprise",
            "aud" => BOX_CONFIG["auth_url"],
            "jti" => base64_encode(random_bytes(64)),
            "exp" => time() + 45,
            "kid" => BOX_CONFIG["public_key_id"],
        ];
        $assertion = JWT::encode($claims, $key, "RS512");
        $params = array(
            "grant_type" => "urn:ietf:params:oauth:grant-type:jwt-bearer",
            "client_id" => BOX_CONFIG["client_id"],
            "assertion" => $assertion,
            "client_secret" => BOX_CONFIG["client_secret"],
        );

        /* Request access token from Box */
        $this->request("POST", CONTENT_TYPE["form-urlencoded"], BOX_CONFIG["auth_url"], $params);
        if ($this->response["success"]) {
            $this->response["data"]->requestTime = $requestTime;

            /* Save access token */
            $tokenFile = fopen(config_item("access_token_path"), "w");
            fwrite($tokenFile, json_encode($this->response["data"]));

            return $this->response["data"]->access_token;
        }
        /* Can not access to box */
        redirect("errors/box_can_not_access");
    }

    /**
     * Load access token from file
     *
     * @return string $accessToken
     */
    public function loadToken()
    {
        if (file_exists(APPPATH . "/cache/token.json")) {
            $accessTokenFile = json_decode(file_get_contents(APPPATH . "/cache/token.json", "w"));
            if (($accessTokenFile->expires_in + $accessTokenFile->requestTime) <= time()) {
                return $this->getToken();
            }
            return $accessTokenFile->access_token;
        }
        return $this->getToken();
    }

    /**
     * Create folder
     * https://developer.box.com/reference/post-folders/
     *
     * @param string $name
     * @param string $parentId
     * @return array $response
     */
    public function createFolder($name, $parentId)
    {
        if ($name !== null && $parentId !== null) {
            $url = "https://api.box.com/2.0/folders/";
            $data = array(
                "name" => $name,
                "parent" => array(
                    "id" => $parentId,
                ),
            );
            $this->request("POST", CONTENT_TYPE["json"], $url, $data);
        }
        return $this->response;
    }

    /**
     * Update folder name by id
     * https://developer.box.com/reference/put-folders-id/
     *
     * @param string $folderId
     * @param string $name
     * @return array $response
     */
    public function updateFolderName($folderId, $name)
    {
        if ($folderId !== null) {
            $url = "https://api.box.com/2.0/folders/$folderId/";
            $data = array("name" => $name);
            $this->request("PUT", CONTENT_TYPE["json"], $url, $data);
        }
        return $this->response;
    }

    /**
     * Delete folder
     * https://developer.box.com/reference/delete-folders-id/
     *
     * @param string $folderId
     * @return array $response
     */
    public function deleteFolder($folderId)
    {
        if ($folderId !== null) {
            $url = "https://api.box.com/2.0/folders/$folderId?recursive=true";
            $this->request("DELETE", CONTENT_TYPE["json"], $url, null);
            return $this->response;
        }
    }

    /**
     * Get folder items
     * https://developer.box.com/reference/get-folders-id-items/
     *
     * @param string $folderId
     * @param string $fields
     * @return array $response
     */
    public function getFolderItems($folderId, $fields = null)
    {
        if ($folderId !== null) {
            $url = "https://api.box.com/2.0/folders/$folderId/items";
            if ($fields !== null) {
                $url .= "?fields=$fields";
            }
            $this->request("GET", CONTENT_TYPE["json"], $url, null);
            return $this->response;
        }
    }

    /**
     * Upload file to specified folder
     * https://developer.box.com/reference/post-files-content/
     *
     * @param string $name
     * @param string $parentId
     * @param string $filePath
     * @return array $response
     */
    public function uploadFile($name, $parentId, $filePath)
    {
        if ($name !== null && $parentId !== null && $filePath !== null) {
            $boundary = "&";
            $attributes = array(
                "name" => $name,
                "parent" => array("id" => $parentId),
            );
            $multipartForm = array(
                array(
                    "name" => "attributes",
                    "contents" => json_encode($attributes),
                ),
                array(
                    "name" => "file",
                    "contents" => fopen($filePath, "r"),
                ),
            );
            $data = new Psr7\MultipartStream($multipartForm, $boundary);
            $this->request("POST", CONTENT_TYPE["multipart"], BOX_CONFIG["upload_url"], $data);
        }
        return $this->response;
    }

    /**
     * Copy file
     * https://developer.box.com/reference/post-files-id-copy/
     *
     * @param string $fileId
     * @param string $parentId
     * @return array $response
     */
    public function copyFile($fileId, $parentId)
    {
        if ($fileId !== null && $parentId !== null) {
            $url = "https://api.box.com/2.0/files/$fileId/copy/";
            $data = (object) array(
                "parent" => (object) array(
                    "id" => $parentId,
                ),
            );
            $this->request("POST", CONTENT_TYPE["json"], $url, $data);
            return $this->response;
        }
    }

    /**
     * Add share link attribute to file
     * https://developer.box.com/reference/put-files-id--add-shared-link/
     *
     * @param string $fileId
     * @return array $response
     */
    public function addShareLink($fileId)
    {
        if ($fileId !== null) {
            $url = "https://api.box.com/2.0/files/$fileId/";
            $data = (object) array(
                "shared_link" => (object) array(
                    "access" => "collaborators",
                ),
            );
            $this->request("PUT", CONTENT_TYPE["json"], $url, $data);
            return $this->response;
        }
    }

    /**
     * Download file
     * https://developer.box.com/reference/get-files-id-content/
     *
     * @param string $fileId
     * @param string $accId
     * @param time $downloadTime
     * @return array $response
     */
    public function downloadFile($fileId)
    {
        if ($fileId !== null) {
            $url = "https://api.box.com/2.0/files/$fileId/content/";
            $this->request("GET", CONTENT_TYPE["json"], $url, null, true);
            return $this->response;
        }
    }

    /**
     * Download zip
     * https://developer.box.com/reference/post-zip-downloads/
     *
     * @param array $fileIdArr
     * @param string $accId
     * @param time $downloadTime
     * @return array $response
     */
    public function downloadZip($fileIdArr)
    {
        if (is_array($fileIdArr) && count($fileIdArr) !== 0) {
            $url = "https://api.box.com/2.0/zip_downloads/";
            /* Init data */
            $items = array();
            foreach ($fileIdArr as $fileId) {
                array_push($items, (object) array(
                    "type" => "file",
                    "id" => $fileId,
                ));
            }
            $data = (object) array(
                "items" => $items,
            );
            /* Get download link */
            $this->request("POST", CONTENT_TYPE["json"], $url, $data);
            if ($this->response["success"]) {
                $this->request("GET", CONTENT_TYPE["json"], $this->response["data"]->download_url, null, true);
            }
            return $this->response;
        }
    }

    /**
     * Delete file
     * https://developer.box.com/reference/delete-files-id/
     *
     * @param string $fileId
     * @return array $response
     */
    public function deleteFile($fileId)
    {
        $url = "https://api.box.com/2.0/files/$fileId/";
        $this->request("DELETE", CONTENT_TYPE["json"], $url, null);
        return $this->response;
    }

    /**
     * Create collaboration
     * https://developer.box.com/reference/post-collaborations/
     *
     * @param array $item
     * @param array $accessibleBy
     * @param string $role
     * @param string $expiresAt
     * @return array $response
     */
    public function createCollaboration($item, $accessibleBy, $role, $expiresAt = null)
    {
        $url = "https://api.box.com/2.0/collaborations/";
        $data = (object) array(
            "item" => (object) $item,
            "accessible_by" => (object) $accessibleBy,
            "role" => $role,
            "expires_at" => $expiresAt,
        );
        $this->request("POST", CONTENT_TYPE["json"], $url, $data);
        return $this->response;
    }

    /**
     * Update collaboration
     * https://developer.box.com/reference/put-collaborations-id/
     *
     * @param string $id
     * @param string $role
     * @param string $expiresAt
     * @return array $response
     */
    public function updateCollaboration($id, $role, $expiresAt)
    {
        $url = "https://api.box.com/2.0/collaborations/$id/";
        $data = (object) array(
            "expires_at" => $expiresAt,
            "role" => $role,
        );
        $this->request("PUT", CONTENT_TYPE["json"], $url, $data);
        return $this->response;
    }

    /**
     * Delete collaboration
     * https://developer.box.com/reference/delete-collaborations-id/
     *
     * @param string $id
     * @return array $response
     */
    public function deleteCollaboration($collaborationId)
    {
        if ($collaborationId !== null) {
            $url = "https://api.box.com/2.0/collaborations/$collaborationId/";
            $this->request("DELETE", CONTENT_TYPE["json"], $url, null);
            return $this->response;
        }
    }

    /**
     * Add folder metadata
     * https://developer.box.com/reference/post-folders-id-metadata-id-id/
     *
     * @param string $id
     * @param array $data
     * @return array $response
     */
    public function addMetadata($id, $data)
    {
        if ($id !== null) {
            $url = "https://api.box.com/2.0/folders/$id/metadata/enterprise/donorMeta/";
            $this->request("POST", CONTENT_TYPE["json"], $url, $data);
            return $this->response;
        }
    }

    /**
     * Update folder metadata
     * https://developer.box.com/reference/put-folders-id-metadata-id-id/
     *
     * @param string $id
     * @param string $data
     * @return array $response
     */
    public function updateMetadata($id, $data)
    {
        if ($id !== null) {
            $url = "https://api.box.com/2.0/folders/$id/metadata/enterprise/donorMeta/";
            $this->request("PUT", CONTENT_TYPE["json-patch"], $url, $data);
            return $this->response;
        }
    }

    /**
     * Create metadata schema
     * https://developer.box.com/reference/post-metadata-templates-schema/
     *
     * @return array $response
     */
    public function createMetadataTemplate()
    {
        $url = "https://api.box.com/2.0/metadata_templates/schema/";
        $data = (object) array(
            "scope" => "enterprise",
            "displayName" => "donor_meta",
            "fields" => array(
                (object) array(
                    "type" => "string",
                    "key" => "offerInstitution",
                    "displayName" => "提供施設提",
                    "description" => "提供施設",
                    "hidden" => false,
                ),
                (object) array(
                    "type" => "string",
                    "key" => "offerInstitutionPref",
                    "displayName" => "提供施設都道府県",
                    "description" => "提供施設都道府県",
                    "hidden" => false,
                ),
                (object) array(
                    "type" => "string",
                    "key" => "donorFullName",
                    "displayName" => "ドナー氏名（カナ）",
                    "description" => "ドナー氏名（カナ）",
                    "hidden" => false,
                ),
                (object) array(
                    "type" => "string",
                    "key" => "age",
                    "displayName" => "年齢",
                    "description" => "年齢",
                    "hidden" => false,
                ),
                (object) array(
                    "type" => "string",
                    "key" => "sex",
                    "displayName" => "性別",
                    "description" => "性別",
                    "hidden" => false,
                ),
                (object) array(
                    "type" => "string",
                    "key" => "deathReasonMstId",
                    "displayName" => "脳死／心停止",
                    "description" => "脳死／心停止",
                    "hidden" => false,
                ),
                (object) array(
                    "type" => "string",
                    "key" => "message",
                    "displayName" => "連絡事項",
                    "description" => "連絡事項",
                    "hidden" => false,
                ),
            ),
        );
        $this->request("POST", CONTENT_TYPE["json"], $url, $data);
        return $this->response;
    }

    public function getMetadata($id)
    {
        if ($id !== null) {
            $url = "https://api.box.com/2.0/folders/$id/metadata/enterprise/donorMeta/";
            $this->request("GET", CONTENT_TYPE["json"], $url, null);
            return $this->response;
        }
    }

    public function getCollaboration($id)
    {
        $url = "https://api.box.com/2.0/collaborations/$id/";
        $this->request("GET", CONTENT_TYPE["json"], $url, null);
        return $this->response;
    }
}
