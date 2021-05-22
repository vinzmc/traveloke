<div class="row">
    <?php foreach ($data as $row) : ?>
        <div class="col-sm-auto mb-4">
            <div class="card" style="width: 20rem;">
                <img src="<?= base_url("assets/images/" . $row['hotel-photo']) ?>" class="card-img-top">
                <div class="card-body">
                    <h5 class="card-title"><?= $row['hotel-name'] ?></h5>
                    <p class="card-text "><?= $row['hotel-address'] ?></p>
                    <p class="card-text">Available Room : <?= $row['hotel-stock'] ?></p>
                    <p class="card-text">Rp <?= number_format($row['hotel-price'], 0, ",", "."); ?></p>
                    <?php
                    for ($i = 0; $i < $row['rating']; $i++) { ?>
                        <svg style="color: yellow;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16">
                            <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                        </svg>
                    <?php } ?>
                    <!-- button book now -->
                    <a class="nav-link btn btn-primary" href="
                    <?php if (isset($_SESSION['name'])) { 
                            echo base_url("index.php/user"); }
                        else {
                            echo base_url("index.php/login");
                        } ?>">Book Now</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>