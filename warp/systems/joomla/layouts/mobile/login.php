<?php if (JFactory::getUser()->get('guest')) : ?>

    <form action="<?php echo JRoute::_('index.php', true, false); ?>" method="post" name="login" id="form-login" >

        <p>
            <input type="text" name="username" placeholder="<?php echo JText::_('Username') ?>" />
        </p>
        <p>
            <input type="password" name="password" placeholder="<?php echo JText::_('Password') ?>" />
        </p>

        <input type="submit" name="Submit" class="button" value="Login" />

        <input type="hidden" name="option" value="com_users" />
        <input type="hidden" name="task" value="user.login" />
        <input type="hidden" name="return" value="<?php echo base64_encode(JRoute::_( 'index.php', true, false)); ?>" />
        <?php echo JHtml::_('form.token'); ?>
    </form>

<?php else: ?>

    <form action="<?php echo JRoute::_('index.php', true, false); ?>" method="post" name="login" id="form-login">

        Hi <?php echo JFactory::getUser()->get('name'); ?>

        <input type="submit" name="Submit" class="button" value="<?php echo JText::_('JLOGOUT'); ?>" />

        <input type="hidden" name="option" value="com_users" />
        <input type="hidden" name="task" value="user.logout" />
        <input type="hidden" name="return" value="<?php echo base64_encode(JRoute::_( 'index.php', true, false)); ?>" />
        <?php echo JHtml::_('form.token'); ?>
    </form>

<?php endif;