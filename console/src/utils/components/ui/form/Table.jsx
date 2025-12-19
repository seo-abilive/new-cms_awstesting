import React, { useState } from 'react'
import { Button } from '@/utils/components/ui/button'
import { Switch } from '@/utils/components/ui/form/Switch'
import { Textarea } from '@/utils/components/ui/form/Textarea'
import { Select } from '@/utils/components/ui/form/Select'
import { HiMenu } from 'react-icons/hi'
import { v4 as uuidv4 } from 'uuid'
import { DndContext, closestCenter, PointerSensor, useSensor, useSensors } from '@dnd-kit/core'
import {
    SortableContext,
    useSortable,
    verticalListSortingStrategy,
    arrayMove,
} from '@dnd-kit/sortable'
import { CSS } from '@dnd-kit/utilities'
import { restrictToVerticalAxis } from '@dnd-kit/modifiers'
import {
    TableCell,
    TableRow,
    Table as FTable,
    TableBody,
    TableHead,
    TableHeadCell,
} from 'flowbite-react'

const SortableRow = ({ id, handle, children, readonly = false }) => {
    const { attributes, listeners, setNodeRef, transform, transition, isDragging } = useSortable({
        id,
        disabled: readonly,
    })
    const style = {
        transform: CSS.Transform.toString(transform),
        transition,
        background: isDragging ? '#f3f4f6' : undefined,
        opacity: isDragging ? 0.8 : 1,
    }

    return (
        <TableRow ref={setNodeRef} style={style} className={`${isDragging ? 'opacity-80' : ''}`}>
            {!readonly && (
                <TableCell className="p-2 sticky left-0 bg-white z-10">
                    <span
                        className="cursor-grab text-gray-400 hover:text-gray-600"
                        style={{ touchAction: 'none' }}
                        {...attributes}
                        {...listeners}
                    >
                        <HiMenu size={20} />
                    </span>
                </TableCell>
            )}
            {children}
        </TableRow>
    )
}

export const Table = ({
    defaultValue = { columns: 2, items: [] },
    onChange,
    readonly = false,
    disabled = false,
}) => {
    const [items, setItems] = useState(
        (Array.isArray(defaultValue?.items) ? defaultValue.items : []).map((value) => ({
            ...value,
            id: uuidv4(),
        }))
    )
    const [columns, setColumns] = useState(defaultValue?.columns ?? 2)

    const sensors = useSensors(
        useSensor(PointerSensor, {
            activationConstraint: { distance: 5 },
        })
    )

    // 行追加
    const addRow = () => {
        const newRow = Array.from({ length: columns }, () => ({ value: '', is_head: false }))
        const newItems = [...items, { id: uuidv4(), row: newRow }]
        setItems(newItems)
        onChangeTable(columns, newItems)
    }

    // 行削除
    const removeRow = (index) => {
        const newItems = items.filter((_, i) => i !== index)
        setItems(newItems)
        onChangeTable(columns, newItems)
    }

    // 行編集
    const updateRow = (rowIndex, colIndex, key, val) => {
        const newItems = items.map((item, i) => {
            if (i === rowIndex) {
                const newRow = item.row.map((col, j) => {
                    if (j === colIndex) {
                        return { ...col, [key]: val }
                    }
                    return col
                })
                return { ...item, row: newRow }
            }
            return item
        })
        setItems(newItems)
        onChangeTable(columns, newItems)
    }

    const ids = items.map((c) => c.id)

    const handleDragEnd = (event) => {
        const { active, over } = event
        if (!over || active.id === over.id) return
        const oldIndex = items.findIndex((c) => c.id === active.id)
        const newIndex = items.findIndex((c) => c.id === over.id)
        const updated = arrayMove(items, oldIndex, newIndex)
        setItems(updated)
        onChangeTable(columns, updated)
    }

    const onChangeTable = (columns, items) => {
        onChange && onChange({ columns: columns, items: items })
    }

    return (
        <>
            <DndContext
                sensors={sensors}
                collisionDetection={closestCenter}
                onDragEnd={handleDragEnd}
                modifiers={[restrictToVerticalAxis]}
            >
                <SortableContext items={ids} strategy={verticalListSortingStrategy}>
                    <div className="max-w-60 mb-2">
                        <Select
                            items={[
                                { value: 1, label: '1列' },
                                { value: 2, label: '2列' },
                                { value: 3, label: '3列' },
                                { value: 4, label: '4列' },
                                { value: 5, label: '5列' },
                                { value: 6, label: '6列' },
                                { value: 7, label: '7列' },
                                { value: 8, label: '8列' },
                                { value: 9, label: '9列' },
                                { value: 10, label: '10列' },
                            ]}
                            defaultValue={columns}
                            size="xs"
                            onChange={(value) => {
                                setColumns(value)
                                const normalizedItems = items.map((item) => {
                                    const rowLength = item.row.length
                                    if (rowLength < value) {
                                        const newCols = Array.from(
                                            { length: value - rowLength },
                                            () => ({ value: '', is_head: false })
                                        )
                                        return { ...item, row: [...item.row, ...newCols] }
                                    } else if (rowLength > value) {
                                        return { ...item, row: item.row.slice(0, value) }
                                    }
                                    return item
                                })
                                setItems(normalizedItems)
                                onChangeTable(value, normalizedItems)
                            }}
                        />
                    </div>
                    {items.length > 0 && (
                        <>
                            <div className="overflow-x-auto">
                                <FTable>
                                    <TableHead>
                                        <TableRow>
                                            {!readonly && !disabled && (
                                                <TableHeadCell className="sticky left-0 bg-white z-10"></TableHeadCell>
                                            )}
                                            {Array.from({ length: columns }, (_, i) => (
                                                <TableHeadCell className="min-w-60" key={i}>
                                                    列{i + 1}
                                                </TableHeadCell>
                                            ))}
                                            {!readonly && !disabled && (
                                                <TableHeadCell className="sticky right-0 bg-white z-10 min-w-30"></TableHeadCell>
                                            )}
                                        </TableRow>
                                    </TableHead>
                                    <TableBody>
                                        {items.map((item, idx) => (
                                            <SortableRow key={item.id} id={item.id}>
                                                {Array.from({ length: columns }, (_, i) => (
                                                    <TableCell className="min-w-60" key={i}>
                                                        <Textarea
                                                            defaultValue={
                                                                item.row?.[i]?.value || ''
                                                            }
                                                            onChange={(value) =>
                                                                updateRow(idx, i, 'value', value)
                                                            }
                                                            readOnly={readonly || disabled}
                                                            disabled={readonly || disabled}
                                                        />
                                                        <Switch
                                                            label="見出し"
                                                            className="mt-2"
                                                            checked={
                                                                item.row?.[i]?.is_head || false
                                                            }
                                                            defaultValue={
                                                                item.row?.[i]?.is_head || false
                                                            }
                                                            onChange={(checked) =>
                                                                updateRow(
                                                                    idx,
                                                                    i,
                                                                    'is_head',
                                                                    checked
                                                                )
                                                            }
                                                            disabled={readonly || disabled}
                                                        />
                                                    </TableCell>
                                                ))}
                                                <TableCell className="sticky right-0 bg-white z-10">
                                                    <Button
                                                        size="sm"
                                                        color="red"
                                                        outline
                                                        onClick={() => removeRow(idx)}
                                                    >
                                                        削除
                                                    </Button>
                                                </TableCell>
                                            </SortableRow>
                                        ))}
                                    </TableBody>
                                </FTable>
                            </div>
                        </>
                    )}
                    {!readonly && !disabled && (
                        <div className="mt-2">
                            <Button size="xs" outline onClick={addRow}>
                                ＋行を追加
                            </Button>
                        </div>
                    )}
                </SortableContext>
            </DndContext>
        </>
    )
}
