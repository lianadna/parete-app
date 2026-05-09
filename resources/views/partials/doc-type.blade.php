@php
  $map = ['pdf' => 'pdf', 'doc' => 'doc', 'docx' => 'doc', 'xls' => 'xls', 'xlsx' => 'xls'];
  $t = $map[strtolower((string) $ext)] ?? 'doc';
  $labels = ['pdf' => 'PDF', 'doc' => 'DOC', 'xls' => 'XLS'];
@endphp
<span class="doc-type {{ $t }}">{{ $labels[$t] ?? strtoupper($t) }}</span>
