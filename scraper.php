<?php

$url = isset($_POST['url']) ? htmlspecialchars($_POST['url']) : '';


if (!empty($url)) {
    $json = json_encode(['url' => implode(";", explode("\r\n", $url))]);
    $emails = [];
    try {
        $ch = curl_init();
        if ($ch === false) {
            throw new Exception('failed to initialize');
        }
        $scraperUrl = 'https://cnl.alteredu.it/email-scraper';
        curl_setopt($ch, CURLOPT_URL, $scraperUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_TIMEOUT, 99999999);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json)
        ));
        $content = curl_exec($ch);

        // Check the return value of curl_exec(), too
        if ($content === false) {
            throw new Exception(curl_error($ch), curl_errno($ch));
        }

        $emails = json_decode($content);

        $filename = 'export_email.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '";');
        $output = '';
        foreach ($emails as $line) {
            $output .= rtrim($line, ",") . "\n";
        }
        echo $output;
        exit;
    } catch (Exception $e) {

        trigger_error(sprintf(
            'Curl failed with error #%d: %s',
            $e->getCode(), $e->getMessage()),
            E_USER_ERROR);

    } finally {
        // Close curl handle unless it failed to initialize
        if (is_resource($ch)) {
            curl_close($ch);
        }
    }

}
?>
<form method="post">
    <textarea type="text" name="url" style="width:400px;height: 300px"></textarea>
    <br>
    <input type="submit" value="Show Emails"/>
</form>

