<div id="leftnav">
    <div class="box2 box_style1">
        <h3><?php echo __('Search Filters')?></h3>
        <ul class="list2" id="global-search-filters">
            <li class="current"><a href="<?php echo $this->request->base?>/search/index/<?php echo $keyword?>/" class="no-ajax"><i class="icon-file-text"></i> <?php echo __('All Results')?></a></li>
            <li><a data-url="<?php echo $this->request->base?>/users/ajax_browse/search?name=<?php echo $keyword?>" id="filter-users" href="#"><i class="icon-user"></i> <?php echo __('People')?></a></li>
            <li><a data-url="<?php echo $this->request->base?>/blogs/ajax_browse/search/<?php echo $keyword?>/" id="filter-blogs" href="#"><i class="icon-edit"></i> <?php echo __('Blogs')?></a></li>
            <li><a data-url="<?php echo $this->request->base?>/albums/ajax_browse/search/<?php echo $keyword?>/" id="filter-albums" href="#"><i class="icon-picture"></i> <?php echo __('Photos')?></a></li>
            <li><a data-url="<?php echo $this->request->base?>/videos/ajax_browse/search/<?php echo $keyword?>/" id="filter-videos" href="#"><i class="icon-facetime-video"></i> <?php echo __('Videos')?></a></li>
            <li><a data-url="<?php echo $this->request->base?>/topics/ajax_browse/search/<?php echo $keyword?>/" id="filter-topics" href="#"><i class="icon-comments"></i> <?php echo __('Topics')?></a></li>
            <li><a data-url="<?php echo $this->request->base?>/groups/ajax_browse/search/<?php echo $keyword?>/" id="filter-groups" href="#"><i class="icon-group"></i> <?php echo __('Groups')?></a></li>
        </ul>
    </div>
</div>

<div id="center">
    <h1><?php echo __('Search Results')?> "<?php echo h($keyword)?>"</h1>
    
    <div id="search-content">
        <?php if ( !empty( $users ) ): ?>
        <h2><?php echo __('People')?></h2>
        <ul class="list1 users_list">
            <?php echo $this->element( 'lists/users_list' ); ?>
        </ul>
        <div style="text-align:right">
            <a href="javascript:void(0)" onclick="globalSearchMore('users')" class="button"><?php echo __('View More Results')?></a>
        </div>   
        <?php endif; ?>
        
        <?php if ( !empty( $groups ) ): ?>
        <h2><?php echo __('Groups')?></h2>
        <ul class="list6 comment_wrapper">
            <?php echo $this->element( 'lists/groups_list' ); ?>
        </ul>
        <div style="text-align:right">
            <a href="javascript:void(0)" onclick="globalSearchMore('groups')" class="button"><?php echo __('View More Results')?></a>
        </div>  
        <?php endif; ?>
        
        <?php if ( !empty( $blogs ) ): ?>
        <h2><?php echo __('Blogs')?></h2>
        <ul class="list6 comment_wrapper">
            <?php echo $this->element( 'lists/blogs_list' ); ?>
        </ul>
        <div style="text-align:right">
            <a href="javascript:void(0)" onclick="globalSearchMore('blogs')" class="button"><?php echo __('View More Results')?></a>
        </div>   
        <?php endif; ?>

        <?php if ( !empty( $albums ) ): ?>
        <h2><?php echo __('Photos')?></h2>
        <ul class="list4 albums">
            <?php echo $this->element( 'lists/albums_list' ); ?>
        </ul>
        <div style="text-align:right">
            <a href="javascript:void(0)" onclick="globalSearchMore('albums')" class="button"><?php echo __('View More Results')?></a>
        </div>  
        <?php endif; ?>
        
        <?php if ( !empty( $videos ) ): ?>
        <h2><?php echo __('Videos')?></h2>
        <ul class="list4 albums">
            <?php echo $this->element( 'lists/videos_list' ); ?>
        </ul>
        <div style="text-align:right">
            <a href="javascript:void(0)" onclick="globalSearchMore('videos')" class="button"><?php echo __('View More Results')?></a>
        </div>  
        <?php endif; ?>
        
        <?php if ( !empty( $topics ) ): ?>
        <h2><?php echo __('Topics')?></h2>
        <ul class="list6 comment_wrapper">
            <?php echo $this->element( 'lists/topics_list' ); ?>
        </ul>
        <div style="text-align:right">
            <a href="javascript:void(0)" onclick="globalSearchMore('topics')" class="button"><?php echo __('View More Results')?></a>
        </div>  
        <?php endif; ?>
        
        
        <?php if ( empty( $users ) && empty( $blogs ) && empty( $albums ) && empty( $videos ) && empty( $topics ) && empty( $groups ) ): ?>
            <div align="center"><?php echo __('Nothing found')?></div>
        <?php endif; ?>
    </div>
</div>