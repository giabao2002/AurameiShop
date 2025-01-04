<?php
$article_id = $_GET['article_id'];
$sql_article = "SELECT * FROM article WHERE article_id = $article_id LIMIT 1";
$query_article = mysqli_query($mysqli, $sql_article);
?>
<!-- start article -->
<section class="article">
    <?php
    while ($row = mysqli_fetch_array($query_article)) {
    ?>
        <div class="article__img">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <div class="article__img--item">
                            <img class="w-100 d-block object-fit-cover" src="admin/modules/blog/uploads/<?php echo $row['article_image'] ?>" alt="image article">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="article__content d-flex justify-center">
                        <div class="article__container pd-section">
                            <h1 class="article__heading h2"><?php echo $row['article_title'] ?></h1>
                            <span class="article__date d-block"><?php echo $row['article_date'] ?></span>
                            <div class="article__share d-flex align-center">
                                <span class="h5">
                                    <ul class="social__items d-flex align-center">
                                        <li class="social__item">
                                            <h3 class="h5">
                                                Share:
                                            </h3>
                                        </li>
                                        <li class="social__item opacity-50">
                                            <a class="" href="#info-product">
                                                <img class="svg__icon d-block" src="./assets/images/icon/facebook.svg" alt="" />
                                            </a>
                                        </li>
                                        <li class="social__item opacity-50">
                                            <a class="" href="#">
                                                <img class="svg__icon d-block" src="./assets/images/icon/twitter.svg" alt="" />
                                            </a>
                                        </li>
                                        <li class="social__item opacity-50">
                                            <a class="" href="#">
                                                <img class="svg__icon d-block" src="./assets/images/icon/instagram.svg" alt="" />
                                            </a>
                                        </li>
                                    </ul>
                                </span>
                            </div>
                            <div class="article__context h4">
                                <?php echo $row['article_content'] ?>
                            </div>
                            <div class="article__footer text-center d-flex align-center justify-center">
                                <img src="./assets/images/icon/arrow-left.svg" alt="back" style="margin-right: 8px;">
                                <a class="h5" href="index.php?page=blog">Trở về danh sách bài viết</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }
    ?>
</section>
<!-- end article -->