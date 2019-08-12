<?php

class Cookie {

    /*********************************************************************************************************************************
    * We use 9 bytes of random data (base64 encoded to 12 characters) for our selector.
    * This provides 72 bits of keyspace and therefore 236 bits of collision resistance (birthday attacks),
    * which is larger than our storage capacity (integer(11) UNSIGNED) by a factor of 16.
    * We use 33 bytes (264 bits) of randomness for our actual authenticator.
    * This should be unpredictable in all practical scenarios.
    * We store an SHA256 hash of the authenticator in the database.
    * This mitigates the risk of user impersonation following information leaks.
    * We re-calculate the SHA256 hash of the authenticator value stored in the user's cookie then compare it with the stored SHA256
    * hash using hash_equals() to prevent timing attacks.
    * We separated the selector from the authenticator because DB lookups are not constant-time.
    * This eliminates the potential impact of timing leaks on searches without causing a drastic performance hit.
    *********************************************************************************************************************************/
    
    public static function set_cookie($user_id) {
        $selector = base64_encode(random_bytes(9));
        $authenticator = random_bytes(33);

        setcookie(
            'remember',
            $selector.':'.base64_encode($authenticator),
            time() + 864000
        );

        // Deleting previous records from auth_tokens
        $query = "DELETE FROM auth_tokens WHERE user_id = :user_id";
        $stmt = Connection::conn()->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        // Inserting the new auth_token
        $query = "INSERT INTO auth_tokens (selector, token, user_id, expires) VALUES (:selector, :token, :user_id, :expires)";
        $stmt = Connection::conn()->prepare($query);
        $stmt->bindParam(':selector', $selector);
        $stmt->bindParam(':token', hash('sha256', $authenticator));
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':expires', date('Y-m-d\TH:i:s', time() + 864000));
        $stmt->execute();
    }


    public static function unset_cookie($user_id = 0) {
        setcookie(
            'remember',
            $_COOKIE['remember'],
            1
        );

        if ($user_id) {
            $query = "DELETE FROM auth_tokens WHERE user_id = :user_id";
            $stmt = Connection::conn()->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
        }
    }


    public static function re_authenticate() {
        list($selector, $authenticator) = explode(':', $_COOKIE['remember']);
        $query = "SELECT * FROM auth_tokens WHERE selector = :selector";
        $stmt = Connection::conn()->prepare($query);
        $stmt->bindParam(':selector', $selector);
        $stmt->execute();
        extract($stmt->fetch(PDO::FETCH_ASSOC));

        /***************************************************************
        * COOKIE IS VALID, THEN DELETE AUTH AND CREATE NEW COOKIE
        ***************************************************************/

        if (hash_equals($token, hash('sha256', base64_decode($authenticator)))) { // Start Cookie Valid
            // Deleting the previous auth_token
            $query = "DELETE FROM auth_tokens WHERE user_id = :user_id";
            $stmt = Connection::conn()->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            // Setting the new cookie
            self::set_cookie($user_id);
            return $user_id;
        } // End Cookie Valid

        /***************************************************************
        * COOKIE IS INVALID REDIRECT THE USER WITH ERROR
        ***************************************************************/

        else { // Start Cookie Invalid
            $_SESSION['error'] = language('unauthorized_login', $_SESSION['lang']);
            self::unset_cookie();
            header('Location: index.php');
            die();
        } // End Cookie Invalid
    }
}
