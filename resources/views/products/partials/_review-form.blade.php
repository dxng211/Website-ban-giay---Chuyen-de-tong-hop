<div style="margin: 20px 0;">
    <h4>Viết nhận xét của bạn</h4>
    <select id="reviewRatingInput" style="margin-top:5px; padding: 6px;">
        <option value="0">Chọn số sao</option>
        <option value="5">5 sao</option>
        <option value="4">4 sao</option>
        <option value="3">3 sao</option>
        <option value="2">2 sao</option>
        <option value="1">1 sao</option>
    </select>
    <textarea id="reviewCommentInput" rows="3" style="width:100%; padding:10px; margin-top:5px;" placeholder="Nhập cảm nhận của bạn..."></textarea>
    <p id="reviewFeedbackMsg" style="font-size: 13px; margin: 6px 0;"></p>
    <button type="button" class="btn" onclick="submitReview()">Gửi Đánh Giá</button>
</div>