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
            'sha' => '3381e2277eae1e7ab31371cf8c3efd88379112e1'
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
            'Authorization: Token githubpersonaltokenhere',
            'Content-Type: application/json'
            )
        );



    $result = curl_exec($ch);




    die;

    }
    /**
     * @Symfony\Component\Routing\Annotation\Route("/curl_test_delete", name="curl_test_delete")
     */
    public function deleteBranch()
    {
        $ch = curl_init("https://api.github.com/repos/MatejMaricic/TestRepo/git/refs/heads/tests");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER, array
            (
                'User-Agent:MatejMaricic',
                'Authorization: Token githubpersonaltokenhere',
                'Content-Type: application/json'
            )
        );



        $result = curl_exec($ch);




        die;
    }
}