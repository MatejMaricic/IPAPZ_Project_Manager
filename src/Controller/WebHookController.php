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
            'ref' => 'refs/head/test',
            'sha' => 'd779acb88bb32f904d6afd5a00cd3ca62321e2ae'
        );

        $push = array(
            'sha' => 'd779acb88bb32f904d6afd5a00cd3ca62321e2ae',
            'force' => 'true'
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
            'Authorization: Token c8955ea79daff0eb59faf984c7ec5364b6210d2f',
            'Content-Type: application/json'
            )
        );



    $result = curl_exec($ch);



    die;

    }
}