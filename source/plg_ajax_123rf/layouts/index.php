<?php
//No direct to access this file
defined('_JEXEC') or die('Restrictedd access');

$file      = dirname(__FILE__) . '/form.xml';
$form      = JForm::getInstance('ja123rf', $file);
$fieldsets = $form->getFieldsets();

$plugin    = JPluginHelper::getPlugin('editors-xtd', '123rfbutton');
$plgParams = new JRegistry($plugin->params);
$imgfolder = JPATH_ROOT . '/' . $plgParams->get('local_save');
?>
<html>
<head>
    <link rel="stylesheet" href="<?php echo JUri::root() . 'media/jui/css/bootstrap.min.css'; ?>"/>
    <link rel="stylesheet" href="<?php echo JUri::root() . 'media/jui/css/chosen.css'; ?>"/>
    <link rel="stylesheet" href="<?php echo JUri::root() . 'plugins/ajax/123rf/assets/css/style.css'; ?>"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo JUri::root() . 'media/jui/js/bootstrap.min.js'; ?>"></script>
    <script type="text/javascript" src="<?php echo JUri::root() . 'plugins/ajax/123rf/assets/js/image-list.js'; ?>"></script>
    <script type="text/javascript" src="<?php echo JUri::root() . 'media/jui/js/chosen.jquery.js'; ?>"></script>
</head>
<body>

<div class="ja123rf-wrapper clearfix">

    <!-- HEADER SECTION -->
    <div class="ja123rf-header">
        <!-- Logo -->
        <div class="ja123rf-logo">
            <img src="<?php echo JUri::root() . 'plugins/ajax/123rf/'; ?>/assets/images/logo.png"/>
        </div>
        <!-- Tabs -->
        <ul id="123rf-tab" class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active">
                <a id="tab-downloaded" class="tab-downloaded" href="#downloaded" aria-controls="downloaded" role="tab" data-toggle="tab">
                    <img src="<?php echo JUri::root() . 'plugins/ajax/123rf/'; ?>/assets/images/downloaded.png"/>
                    <span>Downloaded</span>
                </a>
            </li>
            <li role="presentation">
                <a id="tab-search" class="tab-search" href="#new" aria-controls="new" role="tab" data-toggle="tab">
                    <img src="<?php echo JUri::root() . 'plugins/ajax/123rf/'; ?>/assets/images/search.png"/>
                    <span>On Stocks</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Right Panel -->
    <div class="ja123rf-content">
        <!-- Tab Panes -->
        <div class="tab-content">
            <!-- Downloaded Tab -->
            <div id="downloaded" class="tab-pane active" role="tabpanel">
                <div id="ja123rf-downloaded">
                    <div class="img-wrap-container">
                        <div class="img-wrap">
                            <div class="img-wrap-inner">
                                <?php if (!JFolder::exists($imgfolder)): ?>
                                    <span class="ja123rf-notice">You haven't downloaded any images</span>
                                <?php else:
                                    $images = JFolder::files($imgfolder);
                                    foreach ($images as $image):
                                        $imglink    = JUri::root() . $plgParams->get('local_save');
                                        $imgname = preg_replace('/\..+/', '', $image);

                                        //get Image URL
                                        $imgURL = $imglink . '/' . $image;
                                        //pass image size array into object
                                        $imgSize = getimagesize($imgURL);
                                        //get File Name
                                        $imgName = basename($imgURL);
                                        //get File Type
                                        $imgType = pathinfo($imgURL, PATHINFO_EXTENSION);
                                        //get array values
                                        $imgWidth   = $imgSize[0];
                                        $imgHeight  = $imgSize[1];
                                        $imgTypeRaw = $imgSize['mime'];
                                        ?>
                                        <div id="ja123rf-image-<?php echo $imgname ?>" class="img-item">
                                            <a href="#" title="<?php echo $imgName; ?>">
                                                <img src="<?php echo $imglink . '/' . $image; ?>"/>
                                                <div class="info clearfix">
                                                    <span class="size"><?php echo($imgWidth . ' x ' . $imgHeight . ' px') ?></span>
                                                    <span class="type <?php echo($imgType) ?>"><?php echo($imgType) ?></span>
                                                </div>
                                            </a>
                                        </div>
                                        <?php
                                    endforeach;
                                endif;
                                ?>
                            </div>
                        </div>
                    </div>
                    <script type="text/javascript">
                        //Run ImageList Plugin
                        jQuery(document).ready(function ($) {
                            var showDlDetails = function ($item, $container) {
                                var img_link = $item.find('img').clone().removeAttr('style').attr('src');
                                var content = '<div class="img-detail">';
                                content += '<div class="img-detail-inner clearfix">';
                                content += '<div class="img-detail-left">';
                                content += '<div class="img-detail-image"><img src="' + img_link + '" /></div>';
                                content += '</div>';
                                content += '<div class="img-detail-right">';
                                content += '<div class="img-detail-custom">';
                                content += '<label for="img-desc">Description</label><textarea type="text" name="img-desc" value=""></textarea>';
                                content += '<label for="img-caption">Caption</label><input type="text" name="img-caption" value="" />';
                                content += '<label for="img-title">Image Title</label><input type="text" name="img-title" value="" />';
                                content += '<div class="img-detail-download">'
                                content += '<input type="hidden" name="img-src" value="' + $item.find('img').attr('src') + '" />';
                                content += '<button class="btn btn-success" onClick="InsertImg();" >Insert</button>';
                                content += '</div>';
                                content += '</div>';
                                content += '</div>';
                                content += '</div>';
                                content += '</div>';
                                $(content).appendTo($container);
                            };
                            $('#ja123rf-downloaded .img-wrap').fitRowImages({
                                click: showDlDetails,
                                click_height: 500
                            });
                        });

                        function InsertImg() {
                            var img_url = $("input[name='img-src']").val();
                            var desc = $("textarea[name='img-desc']").val();
                            var caption = $("input[name='img-caption']").val();
                            var title = $("input[name='img-title']").val();
                            var tag = '';
                            tag = '<figure style="margin:0; padding:0"><img title="' + title + '" src="' + img_url + '" alt="' + desc + '"/>';
                            if (caption != '') {
                                tag += '<figcaption>' + caption + '</figcaption>';
                            }
                            tag += '</figure>';

                            window.parent.jInsertEditorText(tag, window.editor);
                            window.parent.SqueezeBox.close();
                        }
                    </script>
                </div>
            </div>
            <!-- Search Image Tab -->
            <div id="new" class="tab-pane" role="tabpanel">
                <div id="ja123rf-loading">
                    <img src="<?php echo JUri::root() . 'plugins/ajax/123rf/'; ?>assets/images/loader.gif"/>
                </div>
                <div id="ja123rf-content-left" class="ja123rf-content-left">
                    <form class="form-filter">
                        <div class="form-filter-mask"></div>
                        <?php foreach ($fieldsets as $name => $fieldset) : ?>
                            <?php if ($name == "filter") : ?>
                                <div id="control-filter" class="control-form control-<?php echo $name; ?> clearfix">
                                    <?php echo $form->renderFieldSet($name); ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </form>
                    <script>
                        $("#ja123rf_params_category").chosen({
                            width: "100%"
                        });
                    </script>
                </div>
                <div id="ja123rf-content-right" class="ja123rf-content-right">
                    <form id="ja123rf-search-form" class="ja123rf-search-form" class="clearfix">
                        <?php foreach ($fieldsets as $name => $fieldset) : ?>
                            <?php if ($name == "search") : ?>
                                <div class="control-form control-<?php echo $name; ?> clearfix">
                                    <a id="content-left-close" class="btn-hide"></a>
                                    <span class="search-text">Search: </span>
                                    <?php echo $form->renderFieldSet($name); ?>
                                    <div class="control-group">
                                        <button id="btn-search" class="btn btn-success" type="button">Search</button>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </form>
                    <div id="ja123rf-search-result">
                        <!-- Get Search Results Here! -->
                    </div>
                    <!-- End Search Results -->
                    <script>
                        $("#ja123rf_params_media_type").chosen({
                            width: "100%"
                        });
                    </script>
                    <script>
                        $("#ja123rf_params_orderby").chosen({
                            width: "100%"
                        });
                    </script>
                    <script>
                        $(document).ready(function () {
                            //Disable filter if the keyword is null
                            $(".form-filter-mask").addClass("active");
                            $("#ja123rf_params_media_type_chzn").addClass("disabled");
                            $("#ja123rf_params_orderby_chzn").addClass("disabled");
                            $("#ja123rf_params_keyword").keyup(function (event) {
                                var inputValue = $('#ja123rf_params_keyword').val();
                                if (inputValue === "") {
                                    $(".form-filter-mask").addClass("active");
                                    $("#ja123rf_params_media_type_chzn").addClass("disabled");
                                    $("#ja123rf_params_orderby_chzn").addClass("disabled");
                                } else {
                                    $(".form-filter-mask").removeClass("active");
                                    $("#ja123rf_params_media_type_chzn").removeClass("disabled");
                                    $("#ja123rf_params_orderby_chzn").removeClass("disabled");
                                }
                            });
                            $("#content-left-close").click(function () {
                                var windowWidth = $(window).width();
                                var leftPanelWidth = $("#ja123rf-content-left").width();
                                var transitionProperty = "width 300ms ease";
                                if (leftPanelWidth > 0) {
                                    $("#ja123rf-content-left").css({
                                        width: "0",
                                        transition: transitionProperty
                                    });
                                    $(".control-filter").css({
                                        width: "0",
                                        padding: "0"
                                    });
                                    $("#ja123rf-content-right").css({
                                        width: "100%",
                                        transition: transitionProperty
                                    });
                                } else if (windowWidth < 1366) {
                                    $("#ja123rf-content-left").css({
                                        width: "18%",
                                        transition: transitionProperty
                                    });
                                    $(".control-filter").css({
                                        width: "100%",
                                        padding: "20px"
                                    });
                                    $("#ja123rf-content-right").css({
                                        width: "82%",
                                        transition: transitionProperty
                                    });
                                } else {
                                    $("#ja123rf-content-left").css({
                                        width: "15%",
                                        transition: transitionProperty
                                    });
                                    $(".control-filter").css({
                                        width: "100%",
                                        padding: "20px"
                                    });
                                    $("#ja123rf-content-right").css({
                                        width: "85%",
                                        transition: transitionProperty
                                    });
                                }
                            });
                        })
                    </script>
                </div>
            </div>
            <!-- End Image Tab Search-->
            <!-- End Image Details -->
        </div>
    </div>

</div>

<!-- Bootstrap Tabs Javascript -->
<script type="text/javascript">
    $('#123rf-tab').click(function (e) {
        e.preventDefault()
        $(this).tab('show')
    });
</script>
</div>

<script type="text/javascript">
    $(document).ajaxStart(function () {
        $("#ja123rf-loading").css("display", "block");
        $("#ja123rf-search-result").css("display", "block");
    });
    $(document).ajaxComplete(function () {
        $("#ja123rf-loading").css("display", "none");
        $("#ja123rf-search-result").css("display", "block");
    });
    $("a#tab-search").one('click', function () {
        ja123rf_search();
    });
    $(document).ready(function () {
        $("input[name^='ja123rf-params-']").change(function () {
                ja123rf_search();
        });

        document.getElementById("ja123rf-search-form").onsubmit = function () {
            return false;
        };

        $("#btn-search").on('click', function () {
            if ($("#ja123rf_params_keyword").val() ==="") {
                alert('You must enter keyword to search images');
            } else {
                var params = ja123rf_getParams();
                var tag = $("#ja123rf-search-result");
                var url = '<?php echo JUri::base(true) ?>/index.php?option=com_ajax&plugin=123rf&view=search&format=html';
                ja123rf_sendAjax('POST', url, params, tag);
            }
        });
    });

    function ja123rf_nextPage() {
        var params = ja123rf_getParams();
        params.page = parseInt($("#current-page").val()) + 1;
        var url = '<?php echo JUri::base(true) ?>/index.php?option=com_ajax&plugin=123rf&view=search&format=html';
        var tag = $("#ja123rf-search-result");
        ja123rf_sendAjax('POST', url, params, tag);
    }

    function ja123rf_prevPage() {
        if ($("#current-page").val() != '1') {
            var params = ja123rf_getParams();
            params.page = parseInt($("#current-page").val()) - 1;
            var tag = $("#ja123rf-search-result");
            var url = '<?php echo JUri::base(true) ?>/index.php?option=com_ajax&plugin=123rf&view=search&format=html';
            ja123rf_sendAjax('POST', url, params, tag);
        }
    }

    function ja123rf_getParams() {
        var params = $("form").serializeArray();
        var params2 = {};
        for (i = 0; i < params.length; i++) {
            var name = params[i].name.substring(15), value = params[i].value;
            if (/\[\]$/.test(name)) {
                name = name.substr(0, name.length - 2);
                params2[name] = params2[name] ? params2[name] + ',' + value : value;
            } else {
                params2[name] = value;
            }
        }

        return params2;
    }

    function ja123rf_sendAjax(type, url, params, ja123rftag) {
        $.ajax({
            type: type,
            url: url,
            data: params,
            dataType: "html",
            error: function (e) {
                alert(e.message);
            },
            success: function (data) {
                ja123rftag.html(data);
            }
        });
    }

    function ja123rf_search() {
        var ajax_alreadysend = 0;
        var params = ja123rf_getParams();
        var tag = $("#ja123rf-search-result");
        var url = '<?php echo JUri::base(true) ?>/index.php?option=com_ajax&plugin=123rf&view=search&format=html';
        var keyup = $("#ja123rf_params_keyword").val();
        ja123rf_sendAjax('POST', url, params, tag);
    }
    function ja123rf_search_all() {
        var ajax_alreadysend = 0;
        var params = ja123rf_getParams();
        var tag = $("#ja123rf-search-result");
        var url = '<?php echo JUri::base(true) ?>/index.php?option=com_ajax&plugin=123rf&view=search&format=html';
        var keyup = $("#ja123rf_params_keyword").val();
        if(keyup === ""){
            alert("Please insert keyword to search images!");
        }else {
            ja123rf_sendAjax('POST', url, params, tag);
        }
    }
</script>
</body>
</html>
