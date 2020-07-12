<?php
require_once "vendor/autoload.php";

$test = new GUMP_Extended();

$kolom = $test->column("username");

$list_rule = array(
    // RULE, MESSAGE, RIGHT VALUE, WRONG VALUE
    ["contains_list,list1;list2", "Nilai harus berupa list1 atau list2", "list1", "wrong_list"],
    ["required", "Username harus diisi!", "besba", ""],
    ["doesnt_contain_list,list1;list2", "Nilai tidak boleh berupa list1 atau list2", "wrong_list", "list2"],
    ["alpha_numeric", "Username harus berupa huruf atau angka", "fe2e2", "d#hi@Hi23"],
    ["valid_email", "Username harus berupa email", "madam@mail.com", "pret12@mmfas"],
    ["valid_array_size_equal,2", "ukuran array tidak sama dengan 2", [1, 2], [1, 2, 3, 4]],
    ["valid_array_size_lesser,3", "ukuran array tidak kecil dari 3", [1, 2], [1, 2, 3, 4]],
    ["valid_array_size_greater,4", "ukuran array tidak besar dari 4", [1, 2, 3, "dd", "2e"], [3, 4]],
    ["between_len,4;12", "Username harus berisi 4 - 12 karakter", "123456", "1"],
    // ["valid_twitter", "Tidak valid twitter", "@madam", "madam@mail.com"],
);

foreach ($list_rule as $index => $rule) {
    // tes nilai benar
    $kolom->setValidation($rule[0], $rule[1]);
    if ($kolom->checkData(array("username" => $rule[2]))->isError()) {
        echo "FAIL : RULE " . $rule[0] . " WITH VALUE " . $rule[2] . " SHOULD BE PASS VALIDATION!";
        exit;
    }

    // tes nilai salah
    if (!$kolom->checkData(array("username" => $rule[3]))->isError()) {
        echo "FAIL : RULE " . $rule[0] . " WITH VALUE " . $rule[3] . " SHOULD NOT BE PASS VALIDATION!";
        exit;
    }

    // tes pesan error
    if ($kolom->checkData(array("username" => $rule[3]))->isError())
    {
        $pesan_error = $kolom->getErrorsArray();
        if($pesan_error['username'] != $rule[1])
        {
            echo "TEST MESSAGE FAIL \n";
            echo "EXPECTED MESSAGES : ".$rule[1]."\n";
            echo "GOT MESSAGES : ".$pesan_error['username'];
            exit;
        }
    }
    $kolom->removeValidation($rule[0]);
}

echo "TEST PASS!";
