<?php
require_once '/var/www/.structure/library/account/api/tasks/loader.php';
load_page(false, function (Account $account, bool $isLoggedIn) {
    $id = get_form_get("id");

    if (is_numeric($id)) {
        if ($isLoggedIn) {
            $result = $account->getDownloads()->sendFileDownload($id);

            if (!$result->isPositiveOutcome()) {
                account_page_redirect($account, true, $result->getMessage());
            }
        } else {
            account_page_redirect(null, false, "You must be logged in to download this file.");
        }
    } else {
        $token = get_form_get("token");

        if (!empty($token)) {
            $download = $account->getDownloads()->find($token);

            if ($download->isPositiveOutcome()) {
                $download = $download->getObject();
                $tokenAccount = $download->account;

                if (!$tokenAccount->exists()
                    || !$tokenAccount->getDownloads()->sendFileDownload(
                        $download->product_id,
                        $download->token
                    )->isPositiveOutcome()) {
                    exit();
                }
            } else {
                exit();
            }
        } else {
            account_page_redirect(null, false, "You must be logged in to access downloads.");
        }
    }
});