
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Bank Import</title>

  <meta charset="utf-8">

    	<?= $this->Html->charset(); ?>
        <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600' rel='stylesheet' type='text/css'>
    	<title><?= $title_for_layout; ?></title>
    	<?php
            echo $this->Html->meta('icon');
            echo $this->Html->css('bootstrap.min');
            echo $this->Html->css('jquery-ui.min');
            echo $this->Html->css('app');
            echo $this->Html->css('skin-blue');
            echo $this->Html->css('style');
            echo $this->fetch('meta');
            echo $this->fetch('css');
            echo $this->fetch('script');
        ?>
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
</head>
<body>
    <div id="wrapper">
        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <span class="navbar-brand" href="#">OrbitRemit Bank Import</span>
            </div>
            <div class="collapse navbar-collapse navbar-ex1-collapse">
                <ul class="nav navbar-nav side-nav">
                    <li class="<?= $this->params['controller'] == 'imports' ? 'active' : '' ?>">
                        <?php echo $this->Html->link(
                                '<i class="fa fa-bullseye"></i> Import',
                                array('controller' => 'imports', 'action' => 'import'),
                                array('escape' => false)
                            );
                        ?>
                    </li>
                    <li class="<?= $this->params['controller'] == 'matches' ? 'active' : '' ?>">
                        <?php echo $this->Html->link(
                                '<i class="fa fa-bullseye"></i> Match',
                                array('controller' => 'matches', 'action' => 'index'),
                                array('escape' => false)
                            );
                        ?>
                    </li>
                    <li class="<?= $this->params['controller'] == 'approvals' ? 'active' : '' ?>">
                        <?php echo $this->Html->link(
                                '<i class="fa fa-bullseye"></i> Approval',
                                array('controller' => 'approvals', 'action' => 'index'),
                                array('escape' => false)
                            );
                        ?>
                    </li>
                </ul>
                <ul class="nav navbar-nav navbar-right navbar-user">
                    <li class="dropdown messages-dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-envelope"></i> <?php echo CakeSession::read('Auth.User.Account.display_name') ?>  <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <?php foreach($tradingAccounts as $key => $value): ?>
                            <li class="message-preview">
                                <a href="#">
                                    <span class="message"><?php echo $this->Html->link($value, '/authentications/change_account/'.$key); ?></span>
                                </a>
                            </li>
                            <li class="divider"></li>
                            <?php endforeach;?>
                        </ul>
                    </li>

                     <li class="dropdown user-dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> <?php echo $user['first_name'] .' '. $user['last_name'] ?><b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="/authentications/logout"><i class="fa fa-power-off"></i> Log Out</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>

        <div id="page-wrapper">

       	    <?php echo $this->fetch('content'); ?>
        </div>
    </div>
        <?php echo $this->Html->script('libs.min'); ?>
</body>
</html>
