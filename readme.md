# Credit
Credit goes to https://github.com/Wixel/GUMP I'm just adding new function to make process more ease

# What's first?
Read Wixel\GUMP documentation at https://github.com/Wixel/GUMP

# What this?
This is a wixel/GUMP library (Data Validation for PHP) with extended function.
Credit goes to https://github.com/Wixel/GUMP

# Example
```
require_once "vendor/autoload.php";

$user = new GUMP_Extended();

// add column and their rules
$user->column("username")
    ->setValidation("required", "Username harus diisi!")
    ->setValidation("alpha_numeric", "Username harus berupa huruf atau angka")
    ->setValidation("between_len,4;12", "Username harus berisi 4 - 12 karakter")
    ->setFilter("trim") // set filter
    ->setFilter("sanitize_string")  // set filter
    ->setFilter("noise_words");  // set filter

$user->column("password")
    ->setValidation("required", "Password harus diisi!")
    ->setValidation("between_len,4:100", "Panjang password harus 4 - 100 karakter");

$user->column("avatar")
    ->setValidation("required_file", "Avatar harus dipilih!")
    ->setValidation("extension,png;jpg", "Ekstensi gambar hanya png.jpg");


// edit "required" and "alpha_numeric" rule message at "username" column
$user->column("username")
    ->editValidationMessage("required", "New message from username")
    ->editValidationMessage("alpha_numeric", "new message from alpha-numeric");

// remove "required" and "alpha_numeric" rule from column "username"
$user->column("username")
    ->removeValidation("required")
    ->removeValidation("alpha_numeric");

// remove filter noise_words from username column
$user->column("username")
    ->removeFilter("noise_words"); // remove filter

// remove all rule from "username" column
$user->column("username")
    ->removeValidation();

// below result are array that can be used directly into wixel/GUMP library
var_dump($user->generateValidationRules());
var_dump($user->generateErrorMessages());
var_dump($user->generateFilter());

// example of validation data
$data = array(
    "username" => "madam",
    "password" => "12345",
    "avatar" => $_FILES['avatar']
);

if ($user->CheckData($data)->IsError()) {
    var_dump($user->getErrorsArray());
    var_dump($user->getReadableErrors());
} else {
    var_dump($user->getValidatedData());
}

```

# Unit Test/Test Code
Just copy `test/gump_helper_test.php` and run it to do a test.
