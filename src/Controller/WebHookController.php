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
    public function createBranch($id, $name, $type)
    {

        $sha = shell_exec('git rev-parse development');
        $sha = str_replace("\n", "", $sha);
        $branchName = $id.'-'.$name;


        $data = array(
            'ref' => 'refs/heads/'.$type.'/#'.$id. '-'.$name,
            'sha' => $sha
        );

        $postData = json_encode($data);

        $ch = curl_init("https://api.github.com/repos/MatejMaricic/IPAPZ_Project_Manager/git/refs");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array
            (
                'User-Agent:MatejMaricic',
                'Authorization: Token tokengoeshere',
                'Content-Type: application/json'
            )
        );

        curl_exec($ch);
        shell_exec('git branch '.$type.'/#' .$branchName. ' development');

    }
    /**
     * @Symfony\Component\Routing\Annotation\Route("/curl_test_delete", name="curl_test_delete")
     */
    public function deleteBranch($id, $name, $type)
    {
        $branchName = $id.'-'.$name;
        $ch = curl_init(
            'https://api.github.com/repos/MatejMaricic/IPAPZ_Project_Manager/git/refs/heads/'.$type.'/%23'.$id.'-'.$name
        );
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array
            (
                'User-Agent:MatejMaricic',
                'Authorization: Token tokengoeshere',
                'Content-Type: application/json'
            )
        );
        curl_exec($ch);
        shell_exec('git branch -D '.$type.'/#' .$branchName. ' development');


    }
}
