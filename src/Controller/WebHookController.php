<?php
/**
 * Created by PhpStorm.
 * User: matej
 * Date: 3/26/19
 * Time: 11:06 AM
 */

namespace App\Controller;



class WebHookController
{
    /**
     * @Symfony\Component\Routing\Annotation\Route("/curl_test", name="curl_test")
     */
    public function createBranch()
    {
        $data = array(
            'ref' => 'refs/heads/tests',
            'sha' => 'e843357eff86184e69ea1aac0a14dd12ef8f073d'
        );

        $postData = json_encode($data);

        $ch = curl_init("https://api.github.com/repos/MatejMaricic/TestRepo/git/refs");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER, array
            (
            'User-Agent:MatejMaricic',
            'Authorization: Token 12703651c3f1f43e7bff4f2f920e5bd25ec28ba2',
            'Content-Type: application/json'
            )
        );



    $result = curl_exec($ch);


    }
}