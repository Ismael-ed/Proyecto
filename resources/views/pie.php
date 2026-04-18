    <div class="container">
        <?php
        if (isset($error)) {
            echo '<h3 style="color:red;">' . $error . '</h3>';
        }
        ?>
    </div>
    <div class="container">
        <?php
        if (isset($mensaje)) {
            echo '<h3 style="color:green;">' . $mensaje . '</h3>';
        }
        ?>
    </div>    
</body>
</html>