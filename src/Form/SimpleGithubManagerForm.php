<?php

namespace Drupal\simple_github_manager\Form;

use Drupal\Core\Form\FormBase;                   // Базовый класс Form API
use Drupal\Core\Form\FormStateInterface;              // Класс отвечает за обработку данных

/**
 * Наследуемся от базового класса Form API
 *
 * @see \Drupal\Core\Form\FormBase
 */
class SimpleGithubManagerForm extends FormBase {


  public function buildForm(array $form, FormStateInterface $form_state) {


    //    get json repositories GitHub    //

    $ch = curl_init();

    $user_name = 'goodmediadeveloper';
    $token = '1007c15a3a95f985283364c4f53eb9420fd932a0';

    curl_setopt($ch, CURLOPT_URL, 'https://api.github.com/users/goodmediadeveloper/repos');
    curl_setopt($ch, CURLOPT_USERAGENT, 'cURL/php');
    curl_setopt($ch, CURLOPT_USERPWD, $user_name . ':' . $token);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

    $result = json_decode(curl_exec($ch));
    $form['#theme'] = 'simple_github_manager';
    $items = [];


    foreach ($result as $value) {
//      kint($value);exit();

      $form[] = [
        '#prefix' => '<li class="groups_list__item">',
        $item['repo_list']['label'] = [
          '#type' => 'item',
          '#title' => $value->name,
          '#attributes' => ['class' => ['btn_link']],
        ],
        $item['repo_list']['repo_name'] = [
          '#type' => 'hidden',
          '#title' => $value->name,
          '#value' => $value->name,
        ],
        $item['repo_list']['submit'] = [
          '#type' => 'submit',
          '#value' => 'Delete',
          '#name' => $value->name,
          '#attributes' => ['class' => ['btn_del']],
        ],
        '#suffix' => '</li>',
      ];
    }
    return $form;
  }


  public function getFormId() {
    return 'simple_github_manager_form';
  }


  public function submitForm(array &$form, FormStateInterface $form_state) {


    $repo_name = $form_state->getTriggeringElement()['#name'];

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://api.github.com/repos/goodmediadeveloper/' . $repo_name);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'cURL/php');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');


    $headers = [];
    $headers[] = 'Authorization: token 1007c15a3a95f985283364c4f53eb9420fd932a0';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
      echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);

    drupal_set_message(t('The repository ' . $repo_name . ' is completely deleted from your GitHub'));

  }

}
