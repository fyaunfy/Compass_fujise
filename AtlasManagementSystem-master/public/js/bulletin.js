$(function () {

  $('.main_categories').click(function () {
    var category_id = $(this).attr('category_id');
    $('.category_num' + category_id).slideToggle();
  });

  // いいねを押した時
  $(document).on('click', '.like_btn', function (e) {
    e.preventDefault();
    $(this).addClass('un_like_btn');
    $(this).removeClass('like_btn');
    // post_idを取得
    var post_id = $(this).attr('post_id');
    // いいねのIDをcount関数に代入
    var count = $('.like_counts' + post_id).text();
    // 数をINT型にする
    var countInt = Number(count);

    // ajax処理
    $.ajax({
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
      method: "post",
      url: "/like/post/" + post_id,
      data: {//サーバーに送信するデータ
        // いいねした投稿IDを送る
        post_id: $(this).attr('post_id'),
      },
      //通信が成功した時の処理
    }).done(function (res) {
      console.log(res);
      // いいねのカウント
      $('.like_counts' + post_id).text(countInt + 1);
      //通信が失敗した時の処理
    }).fail(function (res) {
      console.log('fail');
    });
  });

  // いいねを外した時
  $(document).on('click', '.un_like_btn', function (e) {
    e.preventDefault();
    $(this).removeClass('un_like_btn');
    $(this).addClass('like_btn');
    // post_idを取得 attrは指定された属性を持ってくるメソッド
    var post_id = $(this).attr('post_id');
    // いいねのIDをcount関数に代入
    var count = $('.like_counts' + post_id).text();
    // 数をINT型にする
    var countInt = Number(count);

    $.ajax({
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
      method: "post",
      url: "/unlike/post/" + post_id,
      data: {
        post_id: $(this).attr('post_id'),
      },
    }).done(function (res) {
      // いいねのカウント
      $('.like_counts' + post_id).text(countInt - 1);
    }).fail(function () {

    });
  });

  

  // 編集画面
  $('.edit-modal-open').on('click',function(){
    $('.js-modal').fadeIn();
    var post_title = $(this).attr('post_title');
    // attr('post_body')になっている所が注意
    var post = $(this).attr('post');
    var post_id = $(this).attr('post_id');
    $('.modal-inner-title input').val(post_title);
    // 取得した投稿内容をモーダルの中身へ渡す
    $('.modal-inner-body textarea').text(post);
    $('.edit-modal-hidden').val(post_id);
    return false;
  });
  $('.js-modal-close').on('click', function () {
    $('.js-modal').fadeOut();
    return false;
  });


});
