<label>Project title</label><input name="title" value="{{ old('title', $project->title ?? '') }}" required maxlength="255">
<label>Description <span class="meta">(optional)</span></label><textarea name="description">{{ old('description', $project->description ?? '') }}</textarea>
<div class="actions"><button class="btn">{{ $button }}</button><a class="btn secondary" href="{{ isset($project) ? route('projects.show', $project) : route('projects.index') }}">Cancel</a></div>
