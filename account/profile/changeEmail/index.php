<?php
require '/var/www/.structure/library/account/api/tasks/loader.php';
load_page(true, function (Account $account, bool $isLoggedIn) {
    if (!$isLoggedIn) {
        $token = get_form_get("token");

        if (!empty($token)) {
            echo "<div class='area'>
                    <div class='area_form'>
                        <form method='post'>
                            <input type='email' name='email' placeholder='New Email Address' minlength=0 maxlength=0>
                            <input type='submit' name='change' value='You Must Be Logged In' class='button' id='blue'>
                        </form>
                    </div>
                </div>";
        } else {
            account_page_redirect(null, false, null);
        }
    } else {
        $token = get_form_get("token");

        if (!empty($token)) {
            $result = $account->getEmail()->completeVerification($token);
            account_page_redirect($account, true, $result->getMessage());
        } else if (isset($_POST["change"])) {
            $result = $account->getEmail()->requestVerification(get_form_post("email"));
            $result = $result->getMessage();

            if (!empty($result)) {
                $account->getNotifications()->add(AccountNotifications::FORM, "green", $result, "1 minute");
            }
        }
        account_page_redirect(null, false, null);
    }
});
