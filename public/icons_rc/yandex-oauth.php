<?php
require dirname(__FILE__) . '/../vendor/autoload.php';

use Yandex\Disk\DiskClient;

$clientId     = 'a9a0268d046a4813bb3a9a8c48c4c502'; // ID приложения
$clientSecret = '5270102aa6e8472db46653a6be31c113'; // Пароль приложения
$redirectUri  = 'http://rc-system.ru/yandex-oauth.php'; // Адрес, на который будет переадресован пользователь после прохождения авторизации
//$redirectUri  = 'http://rocklogistic-system-local.ru/yandex-oauth.php'; // Адрес, на который будет переадресован пользователь после прохождения авторизации

// Формируем ссылку для авторизации
$params = array(
    'client_id'     => $clientId,
    'redirect_uri'  => $redirectUri,
    'response_type' => 'code',

    // Список необходимых приложению в данный момент прав доступа, разделенных пробелом.
    // Права должны запрашиваться из перечня, определенного при регистрации приложения.
    // Узнать допустимые права можно по ссылке https://oauth.yandex.ru/client/<client_id>/info, указав вместо <client_id> идентификатор приложения.
    // Если параметр scope не передан, то токен будет выдан с правами, указанными при регистрации приложения.
    // Параметр позволяет получить токен только с теми правами, которые нужны приложению в данный момент.
    'scope'         => "yadisk:disk cloud_api:disk.app_folder cloud_api:disk.read cloud_api:disk.write cloud_api:disk.info",
);


if ( isset( $_GET['code'] ) ) {

    // Формирование параметров (тела) POST-запроса с указанием кода подтверждения
    $query = array(
        'grant_type'    => 'authorization_code',
        'code'          => $_GET['code'],
        'client_id'     => $clientId,
        'client_secret' => $clientSecret
    );
    $query = http_build_query( $query );

    // Формирование заголовков POST-запроса
    $header = "Content-type: application/x-www-form-urlencoded";

    // Выполнение POST-запроса
    $opts    = array(
        'http' =>
            array(
                'method'  => 'POST',
                'header'  => $header,
                'content' => $query
            )
    );
    $context = stream_context_create( $opts );

    if ( ! $content = @file_get_contents( 'https://oauth.yandex.ru/token', false, $context ) ) {
        $error = error_get_last();
        throw new Exception( 'HTTP request failed. Error: ' . $error['message'] );
    }

    $response = json_decode( $content );

    // Если при получении токена произошла ошибка
    if ( isset( $response->error ) ) {
        throw new Exception( 'При получении токена произошла ошибка. Error: ' . $response->error . '. Error description: ' . $response->error_description );
    }

    $accessToken = $response->access_token; // OAuth-токен с запрошенными правами или с правами, указанными при регистрации приложения.
    $expiresIn   = $response->expires_in; // Время жизни токена в секундах.

    // Токен, который можно использовать для продления срока жизни соответствующего OAuth-токена.
    // https://tech.yandex.ru/oauth/doc/dg/reference/refresh-client-docpage/#refresh-client
    $refreshToken = $response->refresh_token;

    // Сохраняем токен в сессии
    //$_SESSION['yaToken'] = array( 'access_token' => $accessToken, 'refresh_token' => $refreshToken );
    setcookie('yaToken', $accessToken);
    echo '
    <script>
        window.close();
    </script>';
}

elseif ( isset( $_GET['error'] ) ) { // Если при авторизации произошла ошибка

    throw new Exception( 'При авторизации произошла ошибка. Error: ' . $_GET['error']
        . '. Error description: ' . $_GET['error_description'] );
}

if(isset($_COOKIE["yaToken"])){
    $diskClient = new DiskClient($_COOKIE['yaToken']);
    $diskClient->setServiceScheme(DiskClient::HTTPS_SCHEME);
    ?>
    <h2 style="text-align:center;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 700px;
    width: 100%;
    border: 2px dashed #f69c55;">
    Вы привязали Яндекс аккаунт<br><?=$diskClient->getLogin()?>
    </h2>
    <br><br>
    <button style="text-align:center;
    display: block;
    justify-content: center;
    align-items: center;
    width: 100%;" onclick="eraseCookie('yaToken')">Нажмите, чтобы выйти</button>
<?php
}
else {
    echo '<h2 style="text-align:center;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 800px;
    width: 100%;
    border: 2px dashed #f69c55;">
    <a href="https://oauth.yandex.ru/authorize?' . http_build_query( $params ) . '">АВТОРИЗАЦИЯ В ЯНДЕКС</a></h2>';
}
?>
<script>

    function eraseCookie(name) {
        document.cookie = name + '=; Max-Age=0'
        window.close();
    }

</script>
