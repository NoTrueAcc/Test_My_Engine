<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 02.10.2017
 * Time: 18:53
 */

require_once 'start.php';

$request = new \core\Request();
$api = new \library\API();
$result = false;

switch ($request->func)
{
    case 'edit' :
        $result = $api->edit($request->obj, $request->value, $request->name, $request->type);
        break;
    case 'delete' :
        $result = $api->delete($request->obj, $request->id);
        break;
    case 'add_comment' :
        $result = $api->addComment($request->parentId, $request->articleId, $request->text);
        break;
    case 'add_chat_message' :
        $result = $api->addChatMessage($request->text);
        break;
	case 'update_chat' :
		$result = $api->getChatMessages();
		break;
}

if($result !== false)
{
    echo json_encode(array('r' => $result, 'e' => false));
}
else
{
    echo json_encode(array('r' => false, 'e' => true));
}