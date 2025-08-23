<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light text-uppercase" style="font-size: 0.85rem;">
            <tr>
                <th class="ps-4">Tiêu đề</th>
                <th class="text-end">Giá thuê</th>
                <th class="text-center">Diện tích</th>
                <th class="text-center">Mô Tả</th>
                <th>Địa chỉ</th>
                <th class="text-center">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($posts as $post)
                <tr class="transition-all duration-200" style="font-size: 0.9rem;">
                    <td class="ps-4">
                        <strong>{{ $post->title }}</strong><br>
                        <small class="text-muted">{{ $post->post_code }}</small>
                    </td>
                    <td class="text-end text-success fw-bold">
                        {{ number_format((float) preg_replace('/[^\d.]/', '', $post->price)) }} đ
                    </td>
                    <td class="text-center">{{ $post->area }} m²</td>
                    <td class="text-center text-muted">
                        <div class="text-truncate" style="max-width: 200px;">
                            {{ \Illuminate\Support\Str::limit(strip_tags($post->description), 50, '...') }}
                        </div>
                    </td>
                    <td class="text-muted">
                        <div class="text-truncate" style="max-width: 200px;">
                            {{ $post->address }}, {{ $post->district }}, {{ $post->city }}
                        </div>
                    </td>
                    <td class="text-center">
                        @if ($type === 'pending')
                            <form action="{{ route('landlord.posts.approval.approve', $post) }}" method="POST"
                                class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-outline-success mb-1">
                                    <i class="bi bi-check-circle"></i> Duyệt
                                </button>
                            </form>
                            <button class="btn btn-sm btn-outline-danger mb-1" data-bs-toggle="modal"
                                data-bs-target="#rejectModal"
                                data-action="{{ route('landlord.posts.approval.reject', $post) }}">
                                <i class="bi bi-x-circle"></i> Từ chối
                            </button>
                            <a href="{{ route('landlord.posts.approval.show', $post) }}"
                                class="btn btn-sm btn-outline-primary mb-1">
                                <i class="bi bi-eye"></i> Chi tiết
                            </a>
                        @elseif ($type === 'approved')
                            <button class="btn btn-sm btn-outline-warning mb-1" data-bs-toggle="modal"
                                data-bs-target="#hideModal-{{ $post->post_id }}">
                                <i class="bi bi-eye-slash"></i> Ẩn bài
                            </button>
                            <!-- Hide Modal -->
                            <div class="modal fade" id="hideModal-{{ $post->post_id }}" tabindex="-1"
                                aria-labelledby="hideModalLabel-{{ $post->post_id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="{{ route('landlord.posts.approval.hide', $post) }}" method="POST">
                                        @csrf
                                        <div class="modal-content rounded shadow-sm">
                                            <div class="modal-header bg-light border-0">
                                                <h6 class="modal-title fs-6 fw-semibold text-dark"
                                                    id="hideModalLabel-{{ $post->post_id }}">
                                                    📌 Lý do ẩn bài viết
                                                </h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <textarea name="hidden_reason" class="form-control form-control-sm" rows="3" placeholder="Nhập lý do..." required></textarea>
                                            </div>
                                            <div class="modal-footer border-0 bg-light">
                                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                                    data-bs-dismiss="modal">Hủy</button>
                                                <button type="submit" class="btn btn-sm btn-warning">Xác nhận
                                                    ẩn</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @elseif ($type === 'hidden')
                            <form action="{{ route('landlord.posts.approval.unhide', $post) }}" method="POST"
                                class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-outline-success mb-1">
                                    <i class="bi bi-eye"></i> Hiện lại
                                </button>
                            </form>
                            <br>
                            <small class="text-muted">📝 {{ $post->hidden_reason }}</small>
                        @elseif ($type === 'rejected')
                            <span class="badge bg-danger-subtle text-danger">Đã từ chối</span><br>
                            <small class="text-muted">📝 {{ $post->rejected_reason }}</small>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="bi bi-folder-x fs-3"></i><br>
                        Không có bài viết nào.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
