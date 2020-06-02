<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Response;
use App\DriveItem;
use App\UserActivity;
use App\Course;
use App\DataObj;


class Drive
{
    //
    public function getAllDriveTypes($request,$folderId,$response=null){
        $videos = array ('WEBM','MPG','MP2','MPEG','MPE','MPV','OGG','MP4','M4P','M4V','AVI','WMV','MOV','QT','FLV','SWF','AVCHD');
        $images = array('JPEG','JPG','PNG','GIF','TIFF','JPE','JIF','JFIF','JFI','WEBP','RAW','BMP','SVG');
        $audios = array('AAC','AIF','AIFF','IFF','M3U','M4A','MID','MP3','MPA','OGA','RA','WAV','WMA','AA','AA3','AMR','AMZ','APE','CAF','CDA','CFA','EMX','FLAC','GPX','M3U8','M4R','NCOR','NKI','3GA','MP2','VOX','M4P','M4B','MPC','MIO','MIDI');
        $documents = array('DOC','DOCX','ODT','PDF','XLS','XLSX','ODS','PPT','PPTX','TXT');
        $compresed = array('7Z','ARJ','DEB','PKG','RAR','RPM','TAR','GZ','TAR.GZ','Z','ZIP');
        if($response==null){
            $response = new DataObj();
            $response->total = 0;
            $response->images = 0;
            $response->videos = 0;
            $response->audios = 0;
            $response->compresed = 0;
            $response->documents = 0;

            $response->others=0;
        }
        $children_reponse = $this->getFolderChildren($request,$folderId);

        $folder_reponse = $this->getFileInformation($request,$folderId);



        if($folder_reponse->responseCode==200 && $children_reponse->responseCode==200){

            $folder_info = json_decode($folder_reponse->body);


            $children_info = json_decode($children_reponse->body);

            //get every file information in this folder
            for($i=0;$i<count($children_info->items);$i++){

                $item_id = $children_info->items[$i]->id;

                $item_response = $this->getFileInformation($request,$item_id);

                if($item_response->responseCode==200 && $item_id!=$folderId){

                    $item_info = json_decode($item_response->body);

                    if($item_info->mimeType=="application/vnd.google-apps.folder"){

                        $response = $this->getAllDriveTypes($request,$item_id,$response);

                    }else{
                        if(property_exists($item_info,'fileExtension')){
                            $fileExtension = strtoupper($item_info->fileExtension);
                            $response->total++;
                            //images
                            if(in_array($fileExtension,$images)){
                                $response->images++;
                            }else 
                            //videos
                            if(in_array($fileExtension,$videos)){
                                $response->videos++;
                            }else
                            //documents
                            if(in_array($fileExtension,$documents)){
                                $response->documents++;
                            }else 
                            //compresed
                            if(in_array($fileExtension,$compresed)){
                                $response->compresed++;
                            }else 
                            //audios
                            if(in_array($fileExtension,$audios)){
                                $response->audios++;
                            }else{
                                $response->others++;
                            }
                            // if(property_exists($this->driveObj,$fileExtension)){
                            //     $this->driveObj->$fileExtension++;
                            // }else{
                            //     $this->driveObj->$fileExtension=1;
                            // }
                        }
                    }
                }
            }
        }
        return $response;
    }




    public $list;
    public $driveObj;
    public function __construct(){
        $this->driveObj = new DataObj();
        $list=[];
    }

    public function getAllDriveStatics($request,$folderId){

        $children_reponse = $this->getFolderChildren($request,$folderId);

        $folder_reponse = $this->getFileInformation($request,$folderId);



        if($folder_reponse->responseCode==200 && $children_reponse->responseCode==200){

            $folder_info = json_decode($folder_reponse->body);


            $children_info = json_decode($children_reponse->body);

            //get every file information in this folder
            for($i=0;$i<count($children_info->items);$i++){

                $item_id = $children_info->items[$i]->id;

                $item_response = $this->getFileInformation($request,$item_id);

                if($item_response->responseCode==200 && $item_id!=$folderId){

                    $item_info = json_decode($item_response->body);

                    if($item_info->mimeType=="application/vnd.google-apps.folder"){

                        $this->getAllDriveStatics($request,$item_id);

                    }else{
                        if(property_exists($item_info,'fileExtension')){
                            $fileExtension = strtoupper($item_info->fileExtension);
                            if(property_exists($this->driveObj,$fileExtension)){
                                $this->driveObj->$fileExtension++;
                            }else{
                                $this->driveObj->$fileExtension=1;
                            }
                        }
                    }
                }
            }
        }
    }

    //get's all the files and folder inside a folder
    public function getFolderChildren($request,$folderId){
        $api_key = "AIzaSyB0RZFMufhdbZHSXAV_6_NQLwDzWPvjbEw";
        $access_token = $request->session()->get('access_token');
        $curl = curl_init();
        curl_setopt($curl , CURLOPT_URL , "https://www.googleapis.com/drive/v2/files/".$folderId."/children?key=".$api_key);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Authorization: Bearer '.$access_token));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $code =curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $head = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $children_reponse = new Response($httpCode,$head);
        return $children_reponse;
    }

    //gets file information from it's id
    public function getFileInformation($request,$fileId){
        $api_key = "AIzaSyB0RZFMufhdbZHSXAV_6_NQLwDzWPvjbEw";
        $access_token = $request->session()->get('access_token');
        $curl_2 = curl_init();
        curl_setopt($curl_2 , CURLOPT_URL , "https://www.googleapis.com/drive/v2/files/".$fileId."?key=".$api_key);
        curl_setopt($curl_2, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Authorization: Bearer '.$access_token));
        curl_setopt($curl_2, CURLOPT_RETURNTRANSFER, TRUE);
        $head = curl_exec($curl_2);
        $httpCode = curl_getinfo($curl_2, CURLINFO_HTTP_CODE);
        curl_close($curl_2);
        $folder_reponse = new Response($httpCode,$head);
        return $folder_reponse;
    }

    //gets all the files in a folder
    public function getAllItemsInAFolder($request,$folderId,$path){
        $children_reponse = $this->getFolderChildren($request,$folderId);
        $folder_reponse = $this->getFileInformation($request,$folderId);
        if($folder_reponse->responseCode==200 && $children_reponse->responseCode==200){
            $folder_info = json_decode($folder_reponse->body);
            $current_path = $path."".$folder_info->title."/";
            $children_info = json_decode($children_reponse->body);
            //get every file information in this folder
            for($i=0;$i<count($children_info->items);$i++){
                $item_id = $children_info->items[$i]->id;
                $item_response = $this->getFileInformation($request,$item_id);
                if($item_response->responseCode==200 && $item_id!=$folderId){
                    $item_info = json_decode($item_response->body);
                    if($item_info->mimeType=="application/vnd.google-apps.folder"){
                        $this->getAllItemsInAFolder($request,$item_id,$current_path);
                    }else{
                        $itemSize = 0;
                        if(property_exists($item_info,'fileSize')){
                            $itemSize =$item_info->fileSize;
                        }
                        $new_item = new DriveItem($item_id,$current_path,$itemSize);
                        $this->list[]=$new_item;
                    }
                }
            }
        }
    }
    //get's all user activities report inside Drive.
    public function getUserActivity($request,$email){
        $api_key = "AIzaSyB0RZFMufhdbZHSXAV_6_NQLwDzWPvjbEw";
        $access_token = $request->session()->get('access_token');
        $curl_2 = curl_init();
        curl_setopt($curl_2 , CURLOPT_URL ,"https://www.googleapis.com/admin/reports/v1/activity/users/".$email."/applications/drive?key=".$api_key);
        curl_setopt($curl_2, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Authorization: Bearer '.$access_token));
        curl_setopt($curl_2, CURLOPT_RETURNTRANSFER, TRUE);
        $head = curl_exec($curl_2);
        $httpCode = curl_getinfo($curl_2, CURLINFO_HTTP_CODE);
        curl_close($curl_2);
        $reponse = new Response($httpCode,$head);
        return $reponse;
    }

    //get's user activities  type==access
    public function getUserActivities($request,$userEmail){
        $response = $this->getUserActivity($request,$userEmail);
        $user = json_decode($response->body);
        $allActivities = [];
        
        $user_items = [];
        if(property_exists($user,'items')){
            $user_items = $user->items;
        }
        for ($i=0;$i<count($user_items);$i++){
           $date = date("Y-m-d ha",strtotime($user->items[$i]->id->time));
           for($j=0;$j<count($user->items[$i]->events);$j++){
               if($user->items[$i]->events[$j]->type=="access"){
                    $activity = new UserActivity();
                    $activity->event_date = $date;
                    $activity->event_name= $user->items[$i]->events[$j]->name;
                    //this is not gonna be perfect but i will do it because am too lazy
                    //but i mean no one said not to come back here and do something better.
                    for($h=0;$h<count($user->items[$i]->events[$j]->parameters);$h++){
                        if($user->items[$i]->events[$j]->parameters[$h]->name=="doc_id"){
                            $activity->doc_id = $user->items[$i]->events[$j]->parameters[$h]->value;

                        }else if($user->items[$i]->events[$j]->parameters[$h]->name=="doc_type"){
                            $activity->doc_type = $user->items[$i]->events[$j]->parameters[$h]->value;

                        }else if($user->items[$i]->events[$j]->parameters[$h]->name=="doc_title"){
                            $activity->doc_title = $user->items[$i]->events[$j]->parameters[$h]->value;
                        }
                    }
                    $allActivities[]=$activity; 
               }
           }
       }
       return $allActivities;
    }


    public function getUserAcitivitiesInClassroom($request,$email,$courseId){
        $course_api = new Course($request);
        $course_response = $course_api->getCourse($courseId);
        if($course_response->responseCode==200){
            $course_info = json_decode($course_response->body);
            //now we have to classroom's folder in drive
            $folderId=$course_info->teacherFolder->id;
            $this->getAllItemsInAFolder($request,$folderId,"/");
            //now this is all the items in the classroom's folder
            $items_in_classroom = $this->list;

            // echo var_dump($items_in_classroom);
            //get user activities 
            $user_activities = $this->getUserActivities($request,$email);

            // echo var_dump($user_activities);
            //////////////////////////////////////////////////////////// now we have everything
            $items_ids = [];
            //get all items ids in one array
            for($i=0;$i<count($items_in_classroom);$i++){
                // $items_ids[] = $items_in_classroom[$i]->itemId;
                $items_ids[$items_in_classroom[$i]->itemId]=$items_in_classroom[$i]->path;
            }
            // echo "<pre>";
            // echo var_dump($items_ids);
            $classroom_activities =[];
            //delete all the activities that are linked to items wich don't belong to the classfolder
            for($i=0;$i<count($user_activities);$i++){
                $doc_id = $user_activities[$i]->doc_id;
                if(isset($items_ids[$doc_id])){
                    $user_activities[$i]->file_path=$items_ids[$doc_id];
                    $classroom_activities[] = $user_activities[$i];
                    // unset($user_activities[$i]);
                    // $i=0;
                    // $user_activities = array_values($user_activities);
                // }else{
                    // for($j=0;$j<count($items_in_classroom);$j++){
                    //     if($items_in_classroom[$j]->itemId==$doc_id){
                    //         if($items_in_classroom[$j]->path!=null){
                    //             $user_activities[$i]->file_path=$items_in_classroom[$j]->path;
                    //         }
                    //     }
                    // }
                }
            }
        }
        return $classroom_activities;
    }
}
