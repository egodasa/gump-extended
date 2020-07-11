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
$user->Column("username")
    ->SetValidation("required", "Username harus diisi!")
    ->SetValidation("alpha_numeric", "Username harus berupa huruf atau angka")
    ->SetValidation("between_len,4;12", "Username harus berisi 4 - 12 karakter")
    ->SetFilter("trim") // set filter
    ->SetFilter("sanitize_string")  // set filter
    ->SetFilter("noise_words");  // set filter

$user->Column("password")
    ->SetValidation("required", "Password harus diisi!")
    ->SetValidation("between_len,4:100", "Panjang password harus 4 - 100 karakter");

$user->Column("avatar")
    ->SetValidation("required_file", "Avatar harus dipilih!")
    ->SetValidation("extension,png;jpg", "Ekstensi gambar hanya png.jpg");


// edit "required" and "alpha_numeric" rule message at "username" column
$user->Column("username")
    ->EditValidationMessage("required", "New message from username")
    ->EditValidationMessage("alpha_numeric", "new message from alpha-numeric");

// remove "required" and "alpha_numeric" rule from column "username"
$user->Column("username")
    ->RemoveValidation("required")
    ->RemoveValidation("alpha_numeric");

// remove filter noise_words from username column
$user->Column("username")
    ->RemoveFilter("noise_words"); // remove filter

// remove all rule from "username" column
$user->Column("username")
    ->RemoveValidation();

// below result are array that can be used directly into wixel/GUMP library
var_dump($user->GenerateValidationRules());
var_dump($user->GenerateErrorMessages());
var_dump($user->GenerateFilter());

// example of validation data
$data = array(
    "username" => "madam",
    "password" => "12345",
    "avatar" => $_FILES['avatar']
);

if ($user->CheckData($data)->IsError()) {
    var_dump($user->GetErrorsArray());
    var_dump($user->GetReadableErrors());
} else {
    var_dump($user->GetValidatedData());
}

```
