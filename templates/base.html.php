<!DOCTYPE html>
<html class="class">
<head>
    <title><?=$this->e($title)?></title>

    <style>
        a {
            background-color: red;
        }

        table {
            color: orange;
        }
    </style>

    <script>
        const foo = function () {
            global = 'foo';
        };
    </script>

</head>
<body>
<?=$this->section('content')?>
</body>
</html>
