<?php
try {
    require '../../login/autoload.php';

    session_start();

    $request = new CSRFHandler;
    $auth = new AuthorizationHandler;

    if ($request->valid_token() && $auth->isAdmin()) {
        unset($_GET['csrf_token']);
        $columns = array(
            array( 'db' => 'id', 'dt' => 0 ),
            array( 'db' => 'type', 'dt' => 1 ),
            array( 'db' => 'status', 'dt' => 2 ),
            array( 'db' => 'recipient', 'dt' => 3 ),
            array( 'db' => 'response', 'dt' => 4 ),
            array( 'db' => 'timestamp', 'dt' => 5 )
        );

        $data = MailHandler::getUnreadLogs($_GET, $columns);

        echo json_encode($data);
    } else {
        http_response_code(401);
        throw new Exception('Unauthorized');
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode($e->getMessage());
}
