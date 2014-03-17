<?php

class desk extends SlackServicePlugin {
  public $name = "Desk.com";
  public $desc = "Post new cases and case comments from Salesforce's Desk.com";
  public $cfg = array(
    'has_token' => true,
  );

  function onView(){
    return $this->smarty->fetch('view.html');
  }

  function onEdit(){
    $channels = $this->getChannelsList();

    if ($_GET['save']){
        $this->icfg['channel'] = $_POST['channel'];
        $this->icfg['channel_name'] = $channels[$_POST['channel']];
        $this->icfg['botname'] = $_POST['botname'];
        $this->saveConfig();

        header("location: {$this->getViewUrl()}&saved=1");
        exit;
    }

    $this->smarty->assign('channels', $channels);
    return $this->smarty->fetch('edit.html');
  }

  # GET vars      : $req['get']
  # POST vars     : $req['post']
  # Raw POST body : $req['post_body']
  # HTTP headers  : $req['headers']

  function onHook($req){
    if (!$this->icfg['channel']){
      return array(
        'ok'  => false,
        'error' => "No channel configured",
      );
    }

    # Desk, being not so smart, always posts a payload in the 'data'
    # param. Slack, on the other hand, expects that payload to be posted
    # in a variable called 'payload'. So we have to dissect it here.
    $desk_payload = json_decode($req['post']['data'], true);

    # Abort if we didn't receive a payload, or if we couldn't decode it.
    if (!$desk_payload || !is_array($desk_payload)){
      return array(
        'ok'  => false,
        'error' => "No payload received from Desk.com",
      );
    }

    if($desk_payload['type'] == "new_case") {
      $text .= $this->escapeText("#{$desk_payload['id']}: ");
      $text .= $this->escapeLink($desk_payload['url'], "{$desk_payload['subject']}");
      $text .= $this->escapeText(" opened by {$desk_payload['user']}");

      # A more complicated version using attachments would
      # look something like this:
      #
      # $this->postToChannel($text, array(
      #   'channel'       => $this->icfg['channel'],
      #   'username'      => $this->icfg['botname'],
      #   'attachments'   => array(
      #     array(
      #       "text" => "{$desk_payload['description']}",
      #       "color" => "#999999",
      #     ),
      #   )
      # ));

      return $this->sendMessage($text);
    }

    return array(
      'ok'    => true,
      'status'  => "Couldn't interpret payload",
    );
  }

  private function sendMessage($text, $attachment){
    $ret = $this->postToChannel($text, array(
      'channel'       => $this->icfg['channel'],
      'username'      => $this->icfg['botname'],
    ));

    return array(
      'ok'        => true,
      'status'    => "Sent a message",
    );
  }
}