<?php

namespace App\Policies;

use App\Models\DocumentType;
use App\Models\User;

class DocumentTypePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('manage_document_types');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('manage_document_types');
    }

    public function update(User $user, DocumentType $documentType): bool
    {
        return $user->hasPermission('manage_document_types');
    }

    public function delete(User $user, DocumentType $documentType): bool
    {
        return $user->hasPermission('manage_document_types');
    }
}