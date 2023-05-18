<!DOCTYPE html>
<html lang="en">
    <head>
		<meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
        <title>Image slides</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
        <link href="{{ asset('css/jplayer/demo.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('css/jplayer/style.css') }}" rel="stylesheet" type="text/css" />
		<link href='http://fonts.googleapis.com/css?family=Open+Sans+Condensed:700,300,300italic' rel='stylesheet' type='text/css' />
		
		<script type="text/javascript" src="{{ asset('js/jplayer/jquery-1.7.2.min.js') }}" ></script>
		<script type="text/javascript" src="{{ asset('js/jplayer/jquery.jplayer.js') }}" ></script>
    	<script type="text/javascript" src="{{ asset('js/jplayer/jquery.audioslideshow.js') }}" ></script>
        <script>
			$(document).ready(function() {
				$('#audio-slideshow').audioSlideshow();
			});
		</script>
    </head>
    <body>
        <div class="container">
			<!-- Codrops top bar -->            
			<header>
				<h1>Audio Slideshow <span>with jPlayer</span></h1>
			</header>
			<section>
				@if(sizeof($output) >= $numimg)
				<div id="audio-slideshow" class="audio-slideshow" data-audio="{{$music}}" data-audio-duration="{{$dur}}">
					<div class="audio-slides" align="center">
						@for($i = 0;$i < $dur/5;$i++)						
						<img src="{{$output[$i % $numimg][0]}}" alt="{{$output[$i % $numimg][1]}}" data-thumbnail="{{$output[$i % $numimg][0]}}" data-slide-time="{!! ($i * 5) !!}" width="640" height="470">
						@endfor						
					</div>
					<div class="audio-control-interface">
						<div class="play-pause-container">
							<a href="javascript:;" class="audio-play" tabindex="1">Play</a>
							<a href="javascript:;" class="audio-pause" tabindex="1">Pause</a>
						</div>
						<div class="time-container">
							<span class="play-time"></span> / <span class="total-time"></span>
						</div>
						<div class="timeline">
							<div class="timeline-controls"></div>
							<div class="playhead"></div>
						</div>
						<div class="jplayer"></div>
					</div>
				</div>
				@else
				<center><h2>There is no image to show!</h2></center>
				@endif
			</section>
        </div>
    </body>
</html>