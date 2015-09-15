<?php
//No direct to access this file
defined('_JEXEC') or die('Restricted access');

$itemid = $_POST['imgid'];

//Customer info.
$plugin    = JPluginHelper::getPlugin('editors-xtd', '123rfbutton');
$plgParams = new JRegistry($plugin->params);
$apikey    = $plgParams->get('apikey', '');
$secretkey = $plgParams->get('secretkey', '');
$accesskey = $plgParams->get('accesskey', '');
$custid    = $plgParams->get('custid', '');
$apisign   = md5($secretkey . 'accesskey' . $accesskey . 'apikey' . $apikey . 'custid' . $custid . 'method123rf.customer.getCreditCount');

//Get the number of credit of customer
$custURL     = 'http://api.123rf.com/rest/?method=123rf.customer.getCreditCount&accesskey=' . $accesskey . '&apikey=' . $apikey . '&custid=' . $custid . '&apisign=' . $apisign;
$custContent = file_get_contents($custURL);
$custData    = new SimpleXMLElement($custContent);
if ($custData['stat'] == 'ok') :
    $custCredit = $custData->customer->credit['balance'];

    $url = 'http://api.123rf.com/rest/?method=123rf.images.getInfo.V2&apikey=' . urlencode($apikey) . '&imageid=' . urlencode($itemid);
    $content = file_get_contents($url);

    if ($content) :
        $data = new SimpleXMLElement($content);

        //Image informations
        if($data['stat'] == 'ok'):
            $imageType     = $data->image['mediatype'];
            $imageid       = $data->image['id'];
            $contributorid = $data->image->contributor['id'];
            $imagename     = $data->image['filename'];
            $folder        = $data->image['folder'];
            $imagedesc     = $data->image->description;
            $modelreleased = ($data->image->release['model'] == '1') ? 'Yes' : 'No';

            //Image Size
            $sizes      = $data->image->sizes;
            $imagesizes = array();
            foreach ($sizes->size as $size) :
                //Get the image download link
                if ($size['format'] == 'jpg') {
                    $apisign2     = md5($secretkey . 'accesskey' . $accesskey . 'apikey' . $apikey . 'custid' . $custid . 'imageid' . $itemid . 'method123rf.images.download.V2resolution' . $size['label']);
                    $downloadURL  = 'http://api.123rf.com/rest/?method=123rf.images.download.V2&accesskey=' . $accesskey . '&apikey=' . $apikey . '&custid=' . $custid . '&imageid=' . $itemid . '&apisign=' . $apisign2 . '&resolution=' . $size['label'];
                    $size['url']  = $downloadURL;
                    $imagesizes[] = $size;
                }
            endforeach;

            $imageurl = 'http://images.assetsdelivery.com/compings/' . $contributorid . '/' . $folder . '/' . $imagename . '.jpg';
        ?>
        <div id="ja123rf-image-detail" class="img-detail">
            <div class="img-detail-inner clearfix">
                <div class="img-detail-left">
                    <div class="img-detail-image">
                        <img src="<?php echo $imageurl; ?>"/>
                    </div>
                    <div class="img-detail-summary">
                        <p><?php echo $imagedesc; ?></p>
                    </div>
                    <div class="img-detail-specification">
                        <p>Image ID: <span><?php echo $itemid; ?></span></p>
                        <p>Image Type: <span><?php echo $imageType; ?></span></p>
                        <p>Model Released: <span><?php echo $modelreleased; ?></span></p>
                        <p>Copyright: <span><?php echo ucfirst($contributorid); ?></span></p>
                    </div>
                </div>

        <div class="img-detail-right">
            <div class="img-detail-size">
                <table class="fullwidth">
                    <tr>
                        <td class="title"></td>
                        <td class="title">Type</td>
                        <td class="title">Resolution</td>
                        <td class="title">File Size</td>
                        <td class="title">Credits</td>
                    </tr>
                    <?php foreach ($imagesizes as $imagesize) : ?>
                    <tr>
                        <td>
                            <input id="ja123rf-price" class="img-price"
                                type="radio"
                                name="ja123rf-size"
                                data-format="<?php echo $imagesize['format']; ?>"
                                data-href="<?php echo $imagesize['url']; ?>"
                                data-price="<?php echo $imagesize['price']; ?>"
                                data-height="<?php echo $imagesize['height']; ?>"
                                data-width="<?php echo $imagesize['width']; ?>"
                                value="<?php echo $imagesize['label'] . $imagesize['price']; ?>'" />
                        </td>
                        <td>
                            <b><?php echo strtoupper($imagesize['format']); ?></b>
                        </td>
                        <td>
                            <span><?php echo $imagesize['width']; ?>x<?php echo $imagesize['height']; ?>px</span>
                        </td>
                        <td><?php echo $imagesize['rawSize']; ?>MB </td>
                        <td id="price" style="color: red;"><?php echo $imagesize['price']; ?></td>
                    </tr>
                <?php endforeach; ?>
                </table>
            </div>
            <div id="ja123rf-detail-credit-message" class="img-detail-credit-message">You have <?php echo $custCredit; ?> credits</div>
                <div id="ja123rf-detail-custom" class="img-detail-custom">
                    <div class="fullwidth">
                        <label for="img-desc">Image Description</label>
                            <textarea id="img-desc" name="img-desc"><?php echo $imagedesc; ?></textarea>
                        </div>
                        <div class="fullwidth">
                            <label for="img-caption">Caption</label>
                            <input type="text" class="span2" name="img-caption" value=""/>
                        </div>
                        <div class="fullwidth">
                            <label for="img-title">Image Title</label>
                            <input type="text" class="span2" name="img-title" value="" />
                        </div>
                        <div class="img-detail-download">
                          <input id="ja123rf-download-url" type="hidden" value="" />
                          <button id="ja123rf-download" class="img-download btn btn-danger btn-large" onclick="getPhotos($(this).attr('href'));" href="#">Download</button>
                        </div>
                    </div>
                 </div>
              </div>
        </div>
            <script>

                $("input[name='ja123rf-size']").change(function(){
                    var price = parseInt($(this).data("price"));
                    var custCredit = parseInt(<?php echo $custCredit; ?>);
                    var download_link = $(this).data("href");

                    if(price <= custCredit){
                        var custCredit2 = custCredit - price;
                        $("#ja123rf-detail-credit-message").html('You rest ' + custCredit2 +' credits');
                        document.getElementById("ja123rf-download-url").value = download_link;
                    }else {
                        var custCredit2 = price - custCredit;
                        $("#ja123rf-detail-credit-message").html('You need more ' + custCredit2 +' credits to download this photo, please buy more credits to continue!');
                        $("#ja123rf-download").attr("href", download_link);
                    }
                });

                function getPhotos(){
                    var downloadURL = $("#ja123rf-download-url").val();
                    var type = $("input[name='ja123rf-size']:checked").val();
                    var format = $("input[name='ja123rf-size']:checked").data("format");
                    var price = $("input[name='ja123rf-size']:checked").data("price");
                    var height = $("input[name='ja123rf-size']:checked").data("height");
                    var width = $("input[name='ja123rf-size']:checked").data("width");

                    if(parseInt(price) > parseInt(<?php echo $custCredit; ?>)){
                        alert("You are not enough credit to download, please buy more credit to continue")
                    } else if(!type){
                        alert("You haven't selected resolution for download this photo");
                    } else{
                        $.ajax({
                            type: "POST",
                            url: '<?php echo JUri::base(true); ?>/index.php?option=com_ajax&plugin=123rf&view=download&format=text',
                            data: {
                                link: downloadURL,
                                itemid:<?php echo $itemid; ?>,
                                type: type,
                                format: format,
                                height: height,
                                width: width
                            },
                            error: function(e){
                                alert(e.message);
                            },
                            dataType: "text",
                            success: function(data){
                                var link = data;
                                var alt = $("textarea#img-desc").val();
                                var caption = $("input[name='img-caption']").val();
                                var title = $("input[name='img-title']").val();
                                var tag = '';

                                tag = '<figure style="margin: 0; padding: 0;"><img title="'+title+'" alt="'+alt+'" src="'+link+'"/>';
                                if(caption != ""){
                                    tag =+ '<figcaption>'+caption+'</figcaption>';
                                }
                                tag += '</figure>';

                                window.parent.jInsertEditorText(tag, window.editor);
                                window.parent.SqueezeBox.close();
                            }
                        });
                    }
                }
            </script>
        <?php else : ?>
            <div id="ja123rf-image-detail" class="img-detail error">
                <div class="img-detail-inner clearfix">
                    <h2><?php echo $data->err; ?>!</h2>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
<?php else : ?>
    <?php if (preg_match('/signature/', $custData->err)) : ?>
        <h2>Invalid Secret Key</h2>
    <?php else : ?>
        <div id="ja123rf-image-detail" class="img-detail error">
            <div class="img-detail-inner clearfix">
                <h2><?php echo $custData->err; ?></h2>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>
