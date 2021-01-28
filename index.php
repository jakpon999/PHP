<?php include "database.php"; ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <style type="text/css">
        table, th, td {
            border: 1px solid #bdc3c7;
            border-collapse: collapse;
        }

        td {
            width: 20px;
            height: 20px;
        }
    </style>
    <script type="text/javascript">
        function Submit(id) {
            document.getElementById("id").value = id;
            document.getElementById("canvasForm").submit();
            //funkcja ustawiająca wartości formularza
        }
    </script>
</head>
<body>
    <?php
        if (isset($_POST["id"]) && isset($_POST["color"])) {
        	//zabezpiecza przed SQL injection
            $id = mysqli_real_escape_string($con, $_POST["id"]);
            $color = mysqli_real_escape_string($con, $_POST["color"]);

            //próba usunięcia danych o kolorze pixela z bazy danych, razem ze sprawdzaniem błędów
            $query = "DELETE FROM canvas WHERE id='$id'";
            if (!mysqli_query($con, $query)) {
                die("Błąd: " . mysqli_error($con));
            }

            //dodanie pixela, jeżeli dane o nim nie istniały wcześniej w bazie danych
            if(mysqli_affected_rows($con) <= 0) {
                $query = "INSERT INTO canvas (id, color) VALUES('$id', '$color')";
                if (!mysqli_query($con, $query)) {
                    die("Błąd: " . mysqli_error($con));
                }
            }
        }

        //definiuje wielkość obrazka
        define("WIDTH", 30);
        define("HEIGHT", 30);

        //pobieranie danych o wszystkich pikselach z bazy do tablicy $canvas. Jeżeli dany piksel nie istnieje, to w tablicy reprezentuje go zerowa wartość
        $canvas = array_fill(0, WIDTH * HEIGHT, 0);
        $sql = "SELECT * FROM canvas";
        $result = mysqli_query($con, $sql);
        if(mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                $id = intval($row["id"]);
                if($id < count($canvas)) {
                    $canvas[$id] = $row["color"];
                }
            }
        }
    ?>
    <div class="container mt-3">
        <div class="row">
            <div class="col">
                <h1>Pixel Art</h1>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <table>
                	<!--tworzenie tabeli-->
                    <?php for($y = 0; $y < HEIGHT; $y++): ?>
                        <tr>
                            <?php for($x = 0; $x < WIDTH; $x++): ?>
                                <?php $id = $y * WIDTH + $x; ?>
                                <!-- wywołanie funkcji submit z id danej komórki, ustawienie odpowiedniego koloru danej komórki -->
                                <td onclick="Submit(<?php echo($id); ?>)"
                                    <?php if($canvas[$id] !== 0) echo "style=\"background-color: $canvas[$id];\""; ?>></td>
                            <?php endfor; ?>
                        </tr>
                    <?php endfor; ?>
                </table>
            </div>
            <div class="col">           	
                <form method="POST" id="canvasForm" action="index.php">
                    <input type="hidden" name="id" id="id" value="">
                    <label for="color" class="form-label">Wybierz kolor</label>
                    <!-- zapamiętywanie wcześniej użytego koloru -->
                    <input type="color" class="form-control" name="color" value="<?php echo isset($_POST['color']) ? $_POST['color'] : '' ?>">               	
                </form>
            </div>
        </div>
    </div>
</body>