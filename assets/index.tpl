<!DOCTYPE html>
<html lang="zh-CN" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>{{__TITLE__}}</title>
    <meta name="viewport" content="width=device-width,height=device-height,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.13.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/app.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js" charset="utf-8"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-file-download@1.4.6/src/Scripts/jquery.fileDownload.js" charset="utf-8"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/js/bootstrap.bundle.min.js" charset="utf-8"></script>
    <script src="./assets/app.js" charset="utf-8"></script>
    <template id="list_dir">
      <tr>
        <th scope="row" onclick="opendir('{{__PATH__}}')" colspan="2">
          <i class="fas {{__ICON__}} fa-fw"></i>
          {{__NAME__}}
        </th>
      </tr>
    </template>
    <template id="list_item">
      <tr>
        <th scope="row">
          <i class="fas {{__ICON__}} fa-fw"></i>
          {{__NAME__}}
        </th>
        <td>
          <a href="{{__PATH__}}" download="{{__NAME__}}">
            <i class="fas fa-download fa-fw"></i>
          </a>
        </td>
      </tr>
    </template>
    <template id="nav_item">
      <a href="javascript:;" onclick="opendir('{{__PATH__}}')">{{__NAME__}}</a>
    </template>
  </head>
  <body>
    <!-- TITLE -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <a class="navbar-brand" href="javascript:;">{{__TITLE__}}</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
        </ul>
        <span class="navbar-text">
          <a href="{{__SUBTITLE_LINK__}}">{{__SUBTITLE_TEXT__}}</a>
        </span>
      </div>
    </nav>
    <!-- ./TITLE -->

    <!-- Container -->
    <div class="container my-4">

      <!-- NAV_PART -->
      <div class="card my-4">
        <div class="card-body">
          <div class="card-title">
            <h5>文件</h5>
          </div>
          <div id="container">
            <div id="nav">当前位置：</div>
            <hr>
            <div id="content">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th scope="col">文件名</th>
                    <th scope="col">操作</th>
                  </tr>
                </thead>
                <tbody id="list">
                </tbody>
              </table>
              <div id="loading">
                载入文件列表中，请稍候
              </div>
            </div>
          </div>
        </div>
      </div>
    <!-- ./NAV_PART -->

    <!-- FOOTER -->
      <footer class="card">
        <div class="card-body">
          <div class="card-title">
            <h5>关于与鸣谢</h5>
          </div>
          <ul class="nya-list">
            <li>
              项目地址：<a href="{{__AKM_LINK__}}" target="_blank">{{__AKM_TEXT__}}</a>
            </li>
          </ul>
        </div>
      </footer>
    <!-- ./FOOTER -->

    </div>
    <!-- ./Container -->


    <!-- BOTTOM -->
    <p class="bottom text-center grey">
      Powered by <a href="https://github.com/file-browser/php-file-browser">File Browser</a>
    </p>
    <!-- ./BOTTOM -->

  </body>
</html>
