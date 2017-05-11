<?php

public function curlRestRequest($apiQueryString, $jsonDecode = FALSE, array $post_data = null, $service_url = null){

    if(!$service_url) {
        $service_url = 'https://shopping-service-p2017.herokuapp.com/';
    }

    $curl = curl_init($service_url.$apiQueryString);

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);

    $curl_response = curl_exec($curl);

    if ($curl_response === false) {
        $info = curl_getinfo($curl);
        curl_close($curl);
        die('error occured during curl exec. Additioanl info: ' . var_export($info));
    }
    curl_close($curl);

    if($jsonDecode)
    {
        $curl_response = json_decode($curl_response);
    }

    return $curl_response;
}

public function renderAPIResults($data, $asObject = FALSE, $template)
{
    if(!$template) {
        throw new Exception('Must provide a template name before rendering!');
    }

    if($asObject != TRUE) {
        $dataArray = json_decode(json_encode($data), true);
        return $this->render($template, array('data' => $dataArray));
    } else {
        $response = new JsonResponse();
        $response->setData(array(
            'data' => $data
        ));
        return $response;
    }
}

public function apiAction($type, $query){
  $apiQueryString = '/search/' . $type . '?query=' . $query;
  $requestedResults = ($this->curlRestRequest($apiQueryString, TRUE));
  $view = $this->renderAPIResults($requestedResults, TRUE, 'ApiBundle:API:results.json.twig');
  return $view;
}

public function getBook($isbn)
{
  $apiQueryString = '/book' . '?isbn=' . $isbn;
  $requestedResults = ($this->curlRestRequest($apiQueryString, TRUE));
  $view = $this->renderAPIResults($requestedResults, TRUE, 'ApiBundle:API:results.json.twig');
  return $view;
}
