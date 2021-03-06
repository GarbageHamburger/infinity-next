@if (isset($post))
{!! Form::model($post, [
    'url'    => Request::url(),
    'method' => "PATCH",
    'files'  => true,
    'id'     => "mod-form",
    'class'  => "form-mod smooth-box",
]) !!}
@else
<form method="POST" id="post-form" class="form-post" data-widget="postbox" action="{{ route(
    $reply_to ? 'board.thread.reply' : 'board.thread.put',
    ['board' => $board->board_uri,] + ($reply_to
        ? ['post_id' => $reply_to->board_id]
        : []
    ),
    false
)}}" accept-charset="UTF-8" enctype="multipart/form-data">
    <input name="_method" type="hidden" value="PUT" />
@endif
    @if (!isset($post))
    <ul class="post-menu">
        @can('password', [App\Post::class, $board])
        <li class="menu-input menu-password no-js">
            {!! Form::password(
                'password',
                [
                    'id'          => "password",
                    'class'       => "field-control",
                    'maxlength'   => 255,
                    'placeholder' => trans('board.field.password'),
                    'autofill'    => "disabled",
            ]) !!}
        </li>
        @endcan

        <li class="menu-icon menu-icon-minimize require-js">
            <span class="menu-icon-button"></span>
            <span class="menu-icon-text">Minimize</span>
        </li>
        <li class="menu-icon menu-icon-maximize require-js">
            <span class="menu-icon-button"></span>
            <span class="menu-icon-text">Expand</span>
        </li>
        <li class="menu-icon menu-icon-close require-js">
            <span class="menu-icon-button"></span>
            <span class="menu-icon-text">Close</span>
        </li>
    </ul>
    @endif

    <fieldset class="form-fields">
        <legend class="form-legend"><i class="fa fa-reply"></i>{{ trans("board.legend." . implode("+", $actions)) }}</legend>

        @include('widgets.messages')

        @can('author', [App\Post::class, $board])
        <div class="field row-author">
            {!! Form::text(
                'author',
                isset($post) ? $post->author : old('author'),
                [
                    'id'          => "author",
                    'class'       => "field-control",
                    'maxlength'   => 255,
                    'placeholder' => trans('board.field.author')
            ]) !!}
        </div>
        @endcan

        @can('subject', [App\Post::class, $board])
        <div class="field row-subject">
            {!! Form::text(
                'subject',
                old('subject'),
                [
                    'id'          => "subject",
                    'class'       => "field-control",
                    'maxlength'   => 255,
                    'placeholder' => trans('board.field.subject'),
            ]) !!}
        </div>
        @endcan

        @if (isset($post) && $post->capcode_id)
        <div class="field row-capcode">
            <span>{{ "## {$post->capcode->getDisplayName()}" }}</span>
        </div>
        @endif

        @if ($board->hasFlags())
        <div class="field row-submit row-double">
            <select id="flag" class="field-control field-flag" name="flag_id">
                <option value="" selected>@lang('board.field.flag')</option>

                @foreach ($board->getFlags() as $flag)
                    <option value="{!! $flag->board_asset_id !!}">{{{ $flag->asset_name }}}</option>
                @endforeach
            </select>
        @else
        <div class="field row-submit">
        @endif
            {!! Form::text(
                'email',
                old('email'),
                [
                    'id'          => "email",
                    'class'       => "field-control",
                    'maxlength'   => 254,
                    'placeholder' => trans('board.field.email'),
            ]) !!}
        </div>

        <div class="field row-post">
            {!! Form::textarea(
                'body',
                old('body'),
                [
                    'id'           => "body",
                    'class'        => "field-control",
                    'autocomplete' => "off",
            ]) !!}
        </div>

        @if(!isset($post))
        @can('attach', $board)
        <div class="field row-file">
            <div class="dz-container">
                <span class="dz-instructions"><span class="dz-instructions-text"><i class="fa fa-upload"></i>&nbsp;@lang('board.field.file-dz')</span></span>
                <div class="fallback">
                    <input class="field-control" id="file" name="files[]" type="file" multiple />
                    <div class="field-control">
                        <label class="dz-spoiler"><input name="spoilers" type="checkbox" value="1" />&nbsp;@lang('board.field.spoilers')</label>
                    </div>
                </div>
            </div>
        </div>
        @endcan
        @endif

        <div class="field row-captcha" style="display:@can('bypass-captcha') none @else block @endcan;">
            <label class="field-label" for="captcha" data-widget="captcha">
                @cannot('bypass-captcha')
                    {!! captcha() !!}
                @else
                    <img src="" class="captcha">
                    <input type="hidden" name="captcha_hash" value="" />
                @endcan

                {!! Form::text('captcha', "", [
                    'id'           => "captcha",
                    'class'        => "field-control",
                    'placeholder'  => "Security Code",
                    'autocomplete' => "off",
                ]) !!}
            </label>
        </div>

        @can('capcode', [$board, $post ?? null])
        <div class="field row-submit row-double">
            <select id="capcode" class="field-control field-capcode" name="capcode">
                <option value="" selected>@lang('board.field.capcode')</option>

                @foreach (user()->getCapcodes($board) as $role)
                    <option value="{!! $role->role_id !!}">{{{ $role->getCapcodeName() }}}</option>
                @endforeach
            </select>
        @else
        <div class="field row-submit">
        @endcan
            {!! Form::button(
                trans("board.submit." . implode("+", $actions)),
                [
                    'type'      => "submit",
                    'id'        => "submit-post",
                    'class'     => "field-submit",
            ]) !!}
        </div>
    </fieldset>
@if (!isset($form) || $form)
</form>
@endif
