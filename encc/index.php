<!-- index.php -->
<?php
// Directory where encrypted files are stored
$uploadDir = 'uploads/';

// Scan the directory for encrypted files
$files = array_diff(scandir($uploadDir), array('..', '.')); // Exclude . and .. entries

?>
<html>

<head>
    <title>Encrypt/Decrypt File</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
    <script>
        function setFormAction() {
            var action = document.getElementById("action").value;
            var form = document.getElementById("form1");
            if (action === 'c') {
                form.action = 'encrypt.php';
            } else if (action === 'd') {
                form.action = 'decrypt.php';
            }
        }
    </script>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1>Utility to Encrypt/Decrypt a File</h1>
            </div>
        </div>
        <?php if (isset($_GET['error']) && $_GET['error'] != '') { ?>
            <div class="row">
                <div class="col-12 alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            </div>
        <?php } ?>
        <form enctype="multipart/form-data" method="POST" id="form1" name="form1" auto-complete="off" onsubmit="setFormAction()">
            <div class="form-row">
                <div class="form-group">
                    <label for="file">File</label>
                    <input type="file" name="file" id="file" class="form-control-file" required />
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required />
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="action">Action</label>
                    <select name="action" id="action" class="form-control" required>
                        <option value="">-- Choose --</option>
                        <option value="c">Encrypt</option>
                        <option value="d">Decrypt</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <button type="submit" class="btn btn-primary">Execute</button>
                <button type="reset" class="btn btn-reset">Reset</button>
            </div>
        </form>
    </div>
    <div class="container">
        <hr>
        <h1 class="mt-5">List of Encrypted Files</h1>

        <?php if (empty($files)) { ?>
            <p>No encrypted files found.</p>
        <?php } else { ?>
            <ul class="list-group mt-3">
                <?php foreach ($files as $file) { ?>
                    <li class="list-group-item">
                        <a href="decrypt.php?file=<?php echo urlencode($file); ?>"><?php echo htmlspecialchars($file); ?></a>
                    </li>
                <?php } ?>
            </ul>
        <?php } ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>