import { ref, watch, onMounted } from "vue";

export default function usePermissionSelection(form, permissions) {
    const key = ref(null);
    const inputSearchPermission = ref(null);
    const permissionsFilter = ref([]);

    const getPermissionList = () => permissions[key.value] || [];

    const searchPermission = () => {
        const list = getPermissionList();

        if (inputSearchPermission.value) {
            permissionsFilter.value[key.value] = list.filter(permission =>
                permission.name.toLowerCase().includes(inputSearchPermission.value.toLowerCase()) ||
                permission.description.toLowerCase().includes(inputSearchPermission.value.toLowerCase())
            );
        } else {
            permissionsFilter.value[key.value] = list;
        }
    };

    const checkedPermission = (permission) => {
        return form.permissions.includes(permission.id);
    };

    const togglePermission = (permission) => {
        const index = form.permissions.indexOf(permission.id);
        if (index === -1) {
            form.permissions.push(permission.id);
        } else {
            form.permissions.splice(index, 1);
        }
    };

    const addAllPermissions = () => {
        const permissionsToAdd = getPermissionList().map(p => p.id);
        permissionsToAdd.forEach(id => {
            if (!form.permissions.includes(id)) {
                form.permissions.push(id);
            }
        });
    };

    const removeAllPermissions = () => {
        const permissionsToRemove = getPermissionList().map(p => p.id);
        form.permissions = form.permissions.filter(
            id => !permissionsToRemove.includes(id)
        );
    };

    watch(() => key.value, () => {
        inputSearchPermission.value = null;
        searchPermission();
    });

    onMounted(() => {
        searchPermission();
    });

    return {
        key,
        inputSearchPermission,
        permissionsFilter,

        getPermissionList,
        searchPermission,
        checkedPermission,
        togglePermission,
        addAllPermissions,
        removeAllPermissions,
    };
}
