<!-- Beautician Assign Modal -->
<div class="modal fade" id="beauticianAssignModal" tabindex="-1" aria-labelledby="beauticianAssignModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content rounded">
            <div class="modal-header rounded">
                <h5 class="modal-title" id="beauticianAssignModalLabel">Assign Beautician</h5>
                <button type="button" class="btn-close close-modal" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <p class="mb-1"><strong>Service:</strong> <span id="beauticianModalServiceName"></span></p>
                </div>
                <div class="row" id="beauticianCardsContainer">
                    <!-- Beautician cards will be rendered here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn thm-btn-bg thm-btn-text-color rounded btn-sm" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Close</button>
            </div>
        </div>
    </div>
</div>

<style>
.beautician-card {
    border: 2px solid #ddd;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    transition: all 0.3s ease;
    background-color: #fff;
}

.beautician-card:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.beautician-card.assigned {
    background-color: #d4edda;
    border-color: #28a745;
}

.beautician-card.disabled {
    opacity: 0.6;
    background-color: #f8f9fa;
    cursor: not-allowed;
}

.beautician-card.disabled .btn-assign-beautician {
    pointer-events: none;
}

.beautician-info {
    margin-bottom: 10px;
}

.beautician-info p {
    margin-bottom: 5px;
}

.btn-assign-beautician {
    width: 100%;
}
</style>

