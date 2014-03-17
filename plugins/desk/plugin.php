<?php

class desk extends SlackServicePlugin {
  public $name = "Desk.com";
  public $desc = "Post new cases and case comments from Salesforce's Desk.com";
  public $cfg = array(
    'has_token' => true,
  );

  function onView(){
    return $this->smarty->fetch('view.txt');
  }

  function onHook($req){
    # GET vars      : $req['get']
    # POST vars     : $req['post']
    # Raw POST body : $req['post_body']
    # HTTP headers  : $req['headers']

    # Message the POST vars
    $this->postToChannel($req['post'], array(
      'channel' => 'C0260JLAX',
      'channel_name' => '#tests',
      'username' => 'Desk',
    ));

    # Message the POST body
    $this->postToChannel($req['post_body'], array(
      'channel' => 'C0260JLAX',
      'channel_name' => '#tests',
      'username' => 'Desk',
    ));

    # Message the actual headers
    $this->postToChannel($req['headers'], array(
      'channel' => 'C0260JLAX',
      'channel_name' => '#tests',
      'username' => 'Desk',
    ));
  }
}