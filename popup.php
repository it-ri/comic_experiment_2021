<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>地雷報告ページ</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <link rel="stylesheet" href="send_data.css">
  <script src="jquery.min.js"></script>
  <script src="send_tag.js"></script>
  <script src="https://unpkg.com/axios/dist/axios.min.js"></script>


  <!-- ここからログインのためについか(firebase)-->
  <script src="https://www.gstatic.com/firebasejs/8.6.7/firebase-app.js"></script>
  <script src="https://www.gstatic.com/firebasejs/8.6.2/firebase-auth.js"></script>
  <script src="https://www.gstatic.com/firebasejs/8.6.2/firebase-firestore.js"></script>
  <script src="https://www.gstatic.com/firebasejs/ui/4.8.0/firebase-ui-auth.js"></script>
  <link type="text/css" rel="stylesheet" href="https://www.gstatic.com/firebasejs/ui/4.8.0/firebase-ui-auth.css" />

  <script>
    var firebaseConfig = {
      apiKey: "Apikey", //要書き換え（APIKey）
      authDomain: "m1comic01.firebaseapp.com",
      projectId: "m1comic01",
      storageBucket: "m1comic01.appspot.com",
      messagingSenderId: "1022178369611",
      appId: "1:1022178369611:web:7a9086edcbeab2bcc7adce",
      measurementId: "G-FR16T5WBXW"
    };
    // Initialize Firebase
    firebase.initializeApp(firebaseConfig);
    firebase.analytics();


    firebase.auth().setPersistence(firebase.auth.Auth.Persistence.SESSION)
      .then(function () {
        console.log('Initialized!') // 確認用のメッセージ
        return firebase.auth().signInWithEmailAndPassword(email, password);
      })
      .catch(function (error) {
        //Handle Errors here.
        var errorCode = error.code;
        var errorMessage = error.message;
      });

  </script>
  <!--ログインのためについかしたのはここまで-->
  <style>
    body {
      width: 300px;
      height: 460px;
    }
  </style>
</head>



<body>
  <button style="margin-left:50%; " id="login" onclick="login()">ログイン</button>
  <p id="login_mes" style="visibility:visible;">ログインしてください🙇</p>
  <div style="visibility: hidden;">
  <p class="title" id="title">testtitleの-1ページ目</p>
  <p>地雷の種類を教えてください</p>
  <textarea style="display: inline;  width:90%; height:30px; text-align: center;" id="input_text" onkeyup="inputCheck()" name="other" value="0" placeholder="登録したい地雷の候補がない時用&#13;(次回からボタンとして表示されます)"></textarea>
  <p>位置を教えてください</p>
  <!-- 0からじゃなくて1からなのはDBがint型だから送ると0消えちゃうから -->
  <input type="checkbox" class="btn" id="btn1" value="1" onclick="Onclick(1)" ;><label class="label" for="btn1">左上</label>
  <input type="checkbox" class="btn" id="btn2" value="2" onclick="Onclick(2)" ;><label class="label" for="btn2">右上</label><br>
  <input type="checkbox" class="btn" id="btn3" value="3" onclick="Onclick(3)" ;><label class="label" for="btn3">左下</label>
  <input type="checkbox" class="btn" id="btn4" value="4" onclick="Onclick(4)" ;><label class="label" for="btn4">右下</label><br>
  <button type="submit" disabled="true" class="send_btn" id="send" onclick="send_info();">送信</button>
  </div>
</body>

</html>

<script>
  //地雷フラグの内容取得用変数と内容取得関数(tag_click)
  var tag_contents = null;

  function tag_click(tag_text) {
    if (tag_text === undefined) {
        tag_contents = "none";
    }else{
        // tag_text = tagの内容
        tag_contents = tag_text;
        if(input_text_flag != "" || tag_contents != null){
            for (let i = 1; i < 5; i++) {
                // 送信ボタン管理
                if (click_count[i] %2!= 0 && click_count[i]!=undefined) {
                    document.getElementById("send").disabled = false;
                    break;
                }else{
                    document.getElementById("send").disabled = true;
                }
            }
        }
    }
  }

  var click_count = new Array();
  //位置ボタンのクリック管理関数
  function Onclick(btn) {
    if (click_count[btn] === undefined) {
      click_count[btn] = 0;
    }
    click_count[btn] = click_count[btn] + 1;
    console.log("クリックされたボタン："+btn, "回数："+click_count[btn]);
    if(input_text_flag != "" || tag_contents != null){
        for (let i = 1; i < 5; i++) {
            if (click_count[i] %2!= 0 && click_count[i]!=undefined) {
                document.getElementById("send").disabled = false;
                break;
            }else{
                document.getElementById("send").disabled = true;
            }
        }
    }


  }
  firebase.auth().onAuthStateChanged(function (user) {
      if (user) {
        document.getElementsByTagName("div")[0].style.visibility ="visible";
        document.getElementById("login_mes").style.visibility ="hidden";

        document.getElementById("login").innerHTML = "ログアウト";
        user_name=user.displayName;

        //ここから　JavaScriptからPHPを呼び出す(APIから情報を取得)
        var request = new XMLHttpRequest();
        var tag_get_url="https://ito.nkmr.io/chrome_ex/search_tag.php?user="+user_name;

        request.open('GET', tag_get_url, true);
        request.responseType = 'json';
        request.addEventListener('load', function (response) {
          console.log('tag load!');
         var tags = request.response;
         for (let s = 0; s < tags.length; s++) {
            // id属性で要素を取得
            // formの中身を取得
            var input_element = document.getElementById('input_text');

            // 新しいHTML要素を作成
            var new_element = document.createElement('input');
            //<input type="checkbox" class="btn"　を作る
            new_element.type ='checkbox';
            new_element.className='btn';
            new_element.id ='tag'+s;
            new_element.value =s;
            // クリックしたらtag_click関数を発火させる
            new_element.setAttribute('onclick', 'tag_click("'+tags[s]['tag']+'")');


            //<label class="label" for="tag'.$i.'">';を実現しよう
            var new_label = document.createElement('label');
            new_label.className='label';
            new_label.htmlFor ='tag'+s;
            new_label.textContent = tags[s]['tag'];

            // 指定した要素の前に挿入
            input_element.before(new_element);
            // 指定した要素の前に挿入
            input_element.before(new_label);
         }
         //改行したいだけ（brタグを作る）
         var br = document.createElement('br');
         document.getElementById('input_text').before(br);
        // JSONデータを受信した後の処理
        });
        request.send();
        //ここまで　JavaScriptからPHPを呼び出す(APIから情報を取得)

      } else {
        document.getElementById("login").innerHTML = "ログイン";
        // ログインしてないとき
      }
    });

// ログイン処理
  function login() {
    var provider = new firebase.auth.GoogleAuthProvider();

    if (document.getElementById("login").innerHTML == "ログイン") {
      firebase.auth().signInWithPopup(provider).then(function (result) {
        var token = result.credential.accessToken;
        var secret = result.credential.secret;
        // The signed-in user info.
        var user = result.user;
        if (user != null) {
          user_name = user.displayName;
          email = user.email;
          photoUrl = user.photoURL;
          emailVerified = user.emailVerified;
          uid = user.uid;  
                  
          //userのデータをDBから取ってくる
          url_user_data='https://ito.nkmr.io/chrome_ex/add_user_data.php?user_name='+user_name+'&user_id='+uid+'&email='+email

        }

        // ...
      }).catch(function (error) {
        // Handle Errors here.
        var errorCode = error.code;
        var errorMessage = error.message;
        // The email of the user's account used.
        var email = error.email;
        // The firebase.auth.AuthCredential type that was used.
        var credential = error.credential;
        // ...
      });
    } else if (document.getElementById("login").innerHTML == "ログアウト") {
        // ログアウト処理
      firebase.auth().signOut();
      location.reload();
    }
    firebase.auth().onAuthStateChanged(function (user) {
      if (user) {
        document.getElementById("login").innerHTML = "ログアウト";
      } else {
        document.getElementById("login").innerHTML = "ログイン";
        // ログインしてない
      }
    });
  }

  var url = ""; 
  var url_tag_add=0;
  var input_text_flag = "";

  function inputCheck() {
    //フォームの中身を取得
    input_text_flag = document.getElementById( "input_text" ).value;
    if(input_text_flag!="") tag_contents=input_text_flag;
    //入力フォームか地雷ボタン，位置ボタンが選択されているなら送信ボタンが押せます
    if(input_text_flag != "" || tag_contents != null){
        // フォームが空じゃないか地雷ボタンが押されているとき
        for (let i = 1; i < 5; i++) {
            // click_count[i]が奇数回（どれかしらが押された）
            if (click_count[i] %2!= 0 && click_count[i]!=undefined) {
                document.getElementById("send").disabled = false;
            }
        }
    }
}

  function send_info() {
    // const tag_id = document.getElementById("tag").value;
    var contents = null;　//タグ新規登録用になった変数
     if (document.getElementById("input_text").value!="") {
     contents = document.getElementById("input_text").value;
     url_tag_add='https://ito.nkmr.io/chrome_ex/add_tag.php?user='+user_name+'&tag='+contents+'&tag_id=-1'
     //tag_idは後でどうにかするための変数
    }
    console.log("tag:" + tag_contents); //今後tag_idにしたい（同じ地雷なら同じidをつけたい）

    var places = -1;  //地雷のある位置を管理する変数

    for (let i = 1; i < 5; i++) {
      if (click_count[i] %2!= 0 && click_count[i]!=undefined) {
        
        if(places==-1) places = String(i);
        else places = String(places) + String(i);
      }
    }
    console.log("place:" + places);

    const titles = document.getElementById('title').innerHTML;
    const title = titles.split('の')[0];
    //半ページで1ページの場合：   const pages = titles.split('側')[1];（その場合はpタグも「-側」の表記を追加）
    const pages = titles.split('の')[1];
    const page = pages.split('ページ')[0];
    console.log("title:" + title);
    console.log("page:" + page);

    if (!user_name) user_name = 'none';

    url = 'https://ito.nkmr.io/chrome_ex/send_tag.php?tag_id=null&page=' + page + '&user=' + user_name + '&place=' + places + '&comic=' + title+'&contents=' + tag_contents;
    console.log(url); //確認用
    if(url_tag_add!=0)console.log(url_tag_add);
// 
    $.ajax({
      type: "POST", //　GETでも可
      url: this.url, //　送り先
      data: {}, //　渡したいデータをオブジェクトで渡す
      dataType: "text", //　データ形式を指定
      scriptCharset: 'utf-8' //　文字コードを指定
    }).done(function (data) {
      $("#return").append('<p>' + data.id + ' : ' + data.school + ' : ' + data.skill + '</p>');
      //alert("送信完了！");
      window.close();
    }).fail(function (XMLHttpRequest, status, e) {
      alert(e);
    });

//tag登録用　ajax通信
$.ajax({
      type: "POST", //　GETでも可
      url: this.url_tag_add, //　送り先
      data: {}, //　渡したいデータをオブジェクトで渡す
      dataType: "text", //　データ形式を指定
      scriptCharset: 'utf-8' //　文字コードを指定
    }).done(function (data) {
      $("#return").append('<p>' + data.id + ' : ' + data.school + ' : ' + data.skill + '</p>');
      //alert("送信完了！");
      window.close();
    }).fail(function (XMLHttpRequest, status, e) {
      alert(e);
    });

//user登録用
    $.ajax({
      type: "POST", //　GETでも可
      url: this.url_user_data, //　送り先
      data: {}, //　渡したいデータをオブジェクトで渡す
      dataType: "text", //　データ形式を指定
      scriptCharset: 'utf-8' //　文字コードを指定
    }).done(function (data) {
      $("#return").append('<p>' + data.id + ' : ' + data.school + ' : ' + data.skill + '</p>');
      //alert("送信完了！");
      window.close();
    }).fail(function (XMLHttpRequest, status, e) {
      alert(e);
    });
  }
</script>
