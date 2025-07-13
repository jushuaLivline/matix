@if(isset($breadcrumb))
	<div class="bread_nav clearfix">
		<ul>
			<li>
				<a href="{{ $breadcrumb['home-url'] }}">{{ $breadcrumb['home-name'] }}</a> <span>&gt;</span>
			</li>
			@if(isset($breadcrumb['li']))
              @foreach($breadcrumb['li'] as $link)
                <li>
                	<a href="{{ $link['url'] }}" style="opacity: 1;">{{ $link['name'] }}</a> <span>&gt;</span>
                </li>
              @endforeach
            @endif
			<li>{{ $breadcrumb['active'] }}</li>
		</ul>
	</div>
@endif