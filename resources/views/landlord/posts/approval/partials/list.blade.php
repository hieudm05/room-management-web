<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light text-uppercase small">
            <tr>
                <th>Tiêu đề</th>
                <th class="text-end">Giá thuê</th>
                <th class="text-center">Diện tích</th>
                <th class="text-center">Mô Tả</th>
                <th>Địa chỉ</th>
                <th class="text-center">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($posts as $post)
                <tr>
                    <td>
                        <strong>{{ $post->title }}</strong><br>
                        <small class="text-muted">{{ $post->post_code }}</small>
                    </td>
                    <td class="text-end text-success fw-bold">
                        {{ number_format((float) preg_replace('/[^\d.]/', '', $post->price)) }} đ
                    </td>
                    <td class="text-center">{{ $post->area }} m²</td>
                    <td class="text-center small text-muted">
                        <div class="text-truncate" style="max-width: 250px;">
                            {{ \Illuminate\Support\Str::limit(strip_tags($post->description), 50, '...') }}
                        </div>
                    </td>
                    <td class="text-muted small">
                        <div class="text-truncate" style="max-width: 250px;">
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
                                data-bs-target="#rejectModal-{{ $post->post_id }}">
                                <i class="bi bi-x-circle"></i> Từ chối
                            </button>

                            <a href="{{ route('landlord.posts.approval.show', $post) }}"
                                class="btn btn-sm btn-outline-info mb-1">
                                <i class="bi bi-eye"></i> Chi tiết
                            </a>

                            <!-- Modal từ chối -->
                            <div class="modal fade" id="rejectModal-{{ $post->post_id }}" tabindex="-1"
                                aria-labelledby="rejectModalLabel-{{ $post->post_id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="{{ route('landlord.posts.approval.reject', $post) }}" method="POST">
                                        @csrf
                                        <div class="modal-content shadow-sm rounded-3">
                                            <div class="modal-header">
                                                <h6 class="modal-title" id="rejectModalLabel-{{ $post->post_id }}">
                                                    📌 Lý do từ chối bài viết
                                                </h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <textarea name="rejected_reason" class="form-control" rows="3" placeholder="Nhập lý do..." required></textarea>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Hủy</button>
                                                <button type="submit" class="btn btn-danger">Xác nhận từ chối</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @elseif ($type === 'approved')
                            <button class="btn btn-sm btn-outline-warning mb-1" data-bs-toggle="modal"
                                data-bs-target="#hideModal-{{ $post->post_id }}">
                                <i class="bi bi-eye-slash"></i> Ẩn bài
                            </button>

                            <!-- Modal ẩn bài -->
                            <div class="modal fade" id="hideModal-{{ $post->post_id }}" tabindex="-1"
                                aria-labelledby="hideModalLabel-{{ $post->post_id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="{{ route('landlord.posts.approval.hide', $post) }}" method="POST">
                                        @csrf
                                        <div class="modal-content shadow-sm rounded-3">
                                            <div class="modal-header">
                                                <h6 class="modal-title" id="hideModalLabel-{{ $post->post_id }}">
                                                    📌 Lý do ẩn bài viết
                                                </h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <textarea name="hidden_reason" class="form-control" rows="3" placeholder="Nhập lý do..." required></textarea>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Hủy</button>
                                                <button type="submit" class="btn btn-warning">Xác nhận ẩn</button>
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
                        <i class="bi bi-folder-x fs-4"></i><br>
                        Không có bài viết nào.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
