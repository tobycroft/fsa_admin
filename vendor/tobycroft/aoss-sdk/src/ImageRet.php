<?php

namespace Tobycroft\AossSdk;

class ImageRet
{
    public mixed $error = null;
    public mixed $data = [];

    public function __construct($response)
    {
        $json = json_decode($response, true);
        if (empty($json) || !isset($json["code"])) {
            $this->error = $response;
        }
        if ($json["code"] == "0") {
            $this->data = $json["data"];
        } else {
            $this->error = $json["data"];
        }
    }

    /**
     * @return mixed
     */
    public function getError(): mixed
    {
        return $this->error;
    }

    public function isSuccess(): bool
    {
        return empty($this->error);
    }

    public function file(): string
    {
        return $this->data;
    }

    public function base64(): string
    {
        return $this->data;
    }

}