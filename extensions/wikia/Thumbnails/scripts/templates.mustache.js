define( 'thumbnails.templates.mustache', [], function() { 'use strict'; return {
    "Thumbnail_articleBlock" : '<figure class="article-thumb {{figureClass}}" style="width: {{width}}px">{{{thumbnail}}}<figcaption><a href="{{url}}" class="sprite details" title="{{thumbnailMore}}"></a>{{#title}}<p class="title">{{title}}</p>{{/title}}{{#caption}}<p class="caption">{{{caption}}}</p>{{/caption}}{{#addedBy}}<p class="attribution">{{{addedBy}}}</p>{{/addedBy}}</figcaption></figure>',
    "Thumbnail_title" : '{{{thumbnail}}}<div class="title" title="{{title}}"><a href="{{url}}">{{title}}</a></div>',
    "done": "true"
  }; });