<?php

require_once("guardiankey.class.php");

// Please run "register.php" for generate your configuration
$GKconfig = array(
    'email' => "",   /* Admin e-mail */
    'agentid' => "",  /* ID for the agent (your system) */
    'key' => "",     /* Key in B64 to communicate with GuardianKey */
    'iv' => "",      /* IV in B64 for the key */
    'service' => "TestServicePHP",      /* Your service name*/
    'orgid' => "",   /* Your Org identification in GuardianKey */
    'authgroupid' => "", /* A Authentication Group identification, generated by GuardianKey */
    'reverse' => "True", /* If you will locally perform a reverse DNS resolution */
);


$GK = new guardiankey($GKconfig);

if (@$_SERVER['SERVER_NAME']) {
    echo "<h2>Any title</h2>
    <p>Please login</p>
    <form action=# method=post>
    <p>Username:<input type=text name=user></p>
    <p>Password:<input type=password name=password></p>
    <input type=submit value=submit>
    </form>";
    if ($_POST) {
        $GK->sendevent($_POST['user']);
        echo "<h2>Any data</h2>";
    }
}else{
    echo "Please an example username: ";
    $handle = fopen("php://stdin", "r");
    $username = trim(fgets($handle));
//     $GK->sendevent($username);
    echo $GK->checkaccess($username);
    echo "\n event sent!";
}



?>

