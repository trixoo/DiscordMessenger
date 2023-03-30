<?php
// Replace with your Discord webhook URL
$webhook_url = "https://discord.com/api/webhooks/1090962535072665631/sPivntZY6rrfItH-pzH3vIRHz7n--X8SOVnR5j0zIIv2JeJkrRJqTLlLNHZ5Q1OiRAVH";

// Load bad words from file
$bad_words = array();
$bad_words_file1 = file("badwords1.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$bad_words_file2 = json_decode(file_get_contents("badwords2.json"), true);
$bad_words = array_merge($bad_words, $bad_words_file1, $bad_words_file2);

function censor_word($word) {
    $first_letter = $word[0];
    $last_letter = $word[strlen($word) - 1];
    $censored = $first_letter;
    for ($i = 1; $i < strlen($word) - 1; $i++) {
        $censored .= "!";
    }
    $censored .= $last_letter;
    return $censored;
}

function censor_message($message) {
    global $bad_words;
    foreach ($bad_words as $bad_word) {
        $pattern = "/\b" . preg_quote($bad_word, "/") . "\b/i";
        $replacement = censor_word($bad_word);
        $message = preg_replace($pattern, $replacement, $message);
    }
    return $message;
}

function censor_username($username) {
    global $bad_words;
    foreach ($bad_words as $bad_word) {
        $pattern = "/\b" . preg_quote($bad_word, "/") . "\b/i";
        $replacement = censor_word($bad_word);
        $username = preg_replace($pattern, $replacement, $username);
    }
    return $username;
}

if (isset($_POST['send'])) {
    $username = $_POST['username'];
    $message = $_POST['message'];

    $censored_username = censor_username($username);
    $censored_message = censor_message($message);

    $sayings = array("exclaims", "whispers", "states", "utters", "voices", "declares", "murmurs");
    $saying = $sayings[array_rand($sayings)];

    $content = '```' . $censored_username . ' ' . $saying . ' "' . $censored_message . '"```';

    // Format the message as JSON
    $data = array("content" => $content, "username" => "Messenger");
    $data_string = json_encode($data);

    // Send the message via cURL
    $ch = curl_init($webhook_url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string))
    );
    $result = curl_exec($ch);

    if ($result === false) {
        // Handle the error here
        $error = curl_error($ch);
    }

    curl_close($ch);
}
header('Location: ' . $_SERVER['HTTP_REFERER']);

