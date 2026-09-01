<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_documents');
    }

    public function view(User $user, Document $document): bool
    {
        return $user->hasPermission('view_documents');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('create_documents');
    }

    public function update(User $user, Document $document): bool
    {
        return $user->hasPermission('edit_documents');
    }

    public function delete(User $user, Document $document): bool
    {
        return $user->hasPermission('archive_documents');
    }

    public function restore(User $user, Document $document): bool
    {
        return $user->hasPermission('archive_documents');
    }

    public function download(User $user, Document $document): bool
    {
        return $user->hasPermission('download_documents');
    }

    public function preview(User $user, Document $document): bool
    {
        return $user->hasPermission('preview_documents');
    }

    public function manageVersions(User $user, Document $document): bool
    {
        return $user->hasPermission('manage_document_versions');
    }
}
