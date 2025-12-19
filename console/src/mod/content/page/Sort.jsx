import { useEffect, useState } from 'react'
import { useContent } from '@/mod/content/utils/context/ContentContext'
import { ResourceSort } from '@/utils/components/common/ResourceSort'

export const Sort = () => {
    const { config, modelData, listConfig } = useContent()
    const breads = [{ name: config.name }, { name: '並び替え' }]
    const [columns, setColumns] = useState(listConfig?.columns || [])
    const [addScopedColumns, setAddScopedColumns] = useState(listConfig?.addScopedColumns || {})

    useEffect(() => {
        if (!modelData) return

        let newColumns = [...columns]
        let newScopedColumns = { ...addScopedColumns }
        newColumns.push({
            key: 'actions',
            label: '',
            sortable: false,
            _props: { style: { width: '10%' } },
        })
        setColumns(newColumns)
        setAddScopedColumns(newScopedColumns)
    }, [modelData])

    return <ResourceSort options={{ breads, config, columns, addScopedColumns }} />
}
