<?php
//No direct to access this file
defined('_JEXEC') or die('Restrictedd access');

$plugin    = JPluginHelper::getPlugin('editors-xtd', '123rfbutton');
$plgParams = new JRegistry($plugin->params);
$apikey    = $plgParams->get('apikey', '');

$params = $_POST;

if ($apikey && $apikey != '') :
    $params = $_POST;
    if ($params['keyword'] == '') {
        if (empty($params['page'])) {
            $params['page'] = '1';
        }
        $fetchURL = 'http://api.123rf.com/rest/?method=123rf.images.getLatestImages&apikey=' . $apikey . '&page=' . $params['page'];
    } else {
        $fetchURL = 'http://api.123rf.com/rest/?method=123rf.images.search&apikey=' . urlencode($apikey);
        foreach ($params as $k => $val) {
            $fetchURL .= "&$k=" . urlencode($val);
        }
    }
    $content = file_get_contents($fetchURL);
    $data    = new SimpleXMLElement($content);

    if ($data && $data['stat'] == 'ok') :

        $imgs = $data->images->image;

        $pages   = $data->images['pages'];
        $current = $data->images['page'];
        if ($data->images['total'] > 0) : ?>
            <div class="ja123rf-paging">
                <button id="btnprev"  class="btn btn-info" onclick="ja123rf_prevPage(); return false;">Prev</button>
                <button id="btnnext"  class="btn btn-info" onclick="ja123rf_nextPage(); return false;">Next</button>
                <span>Go to</span><input id="current-page" size="10" type="text" onchange="gotoPage(); return false;" value="<?php echo $current; ?>"/> <span>of <?php echo $pages; ?></span>
            </div>

            <div class="img-wrap-container">
                <div class="img-wrap">
                    <div class="img-wrap-inner">

                    <?php foreach ($imgs as $item) :
                        // prepare the thumbnail URL
                        $thumbnail = 'http://images.assetsdelivery.com/thumbnails/' . $item['contributorid'] . '/' . $item['folder'] . '/' . $item['filename'] . '.jpg';

                        //get file name
                        $imgName = basename($thumbnail);
                    ?>

                        <!-- output the result as the array is stepped through -->
                        <div id="ja-123rf-<?php echo $item['id']; ?>" class="img-item">
                            <a href="#" title="<?php echo $imgName; ?>">
                                <img title="<?php echo $item['description']; ?>"  src="<?php echo $thumbnail; ?>" alt="<?php echo $item['description']; ?>">
                            </a>
                        </div>
                    <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <script type="text/javascript">
                    //Run ImageList Plugin
                    jQuery(document).ready(function($){
                        var showDetails = function ($item, $container) {
                            var imageid = $item.attr('id').substring(9);
                            $.ajax({
                                type: "POST",
                                url: '<?php echo JUri::base(); ?>/index.php?option=com_ajax&plugin=123rf&view=detail&format=html',
                                data:{
                                    imgid: imageid
                                },
                                error: function(e){
                                    alert(e.message);
                                },
                                dataType: "html",
                                success: function(data){
                                    $(data).appendTo($container);
                                }
                            });
                        }
                        $("#ja123rf-search-result .img-wrap").fitRowImages({
                            click: showDetails,
                            click_height: 660
                        });
                    })

                    //Go to Page
                    function gotoPage(){
                        var params = ja123rf_getParams();
                        params.page = parseInt($("#current-page").val());
                        var tag = $("#ja123rf-search-result");
                        var url = '<?php echo JUri::base(true); ?>/index.php?option=com_ajax&plugin=123rf&view=search&format=html';
                        ja123rf_sendAjax('POST', url, params, tag);
                    }
            </script>
        <?php else : ?>
            <div class="no-result">
                <h2>No Results</h2>
            </div>
        <?php endif; ?>
    <?php else : ?>
        <div class="no-result">
            <h2><?php echo $data->err; ?></h2>
        </div>
    <?php endif; ?>
<?php else : ?>
    <div class="no-result">
        <h2>Please insert your API KEY into the ja123rf buttton plugin configuration!</h2>
    </div>
<?php endif; ?>
