<?php

class Browser {
  private $curl;

  function Browser () {
    $this->curl = curl_init();
    curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($this->curl, CURLOPT_TIMEOUT, 15);
    # curl_setopt($this->curl, CURLOPT_HEADER, true);
  }

  // not implemented here
  function proxify ($proxy="") { return $this;
    set_proxy($this->curl, $proxy);
    return $this;
  }

  function get ($url) {
    curl_setopt($this->curl, CURLOPT_URL, $url);
    return curl_exec($this->curl);
  }
}

?>

