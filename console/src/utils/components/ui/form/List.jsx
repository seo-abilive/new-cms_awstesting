import React, { useState } from 'react'
import { Button } from '@/utils/components/ui/button'
import { TextInput } from '@/utils/components/ui/form/TextInput'
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
        <div
            ref={setNodeRef}
            style={style}
            className={`flex gap-2 mb-2 items-center ${isDragging ? 'opacity-80' : ''}`}
        >
            {!readonly && (
                <span
                    className="cursor-grab text-gray-400 hover:text-gray-600"
                    style={{ touchAction: 'none' }}
                    {...attributes}
                    {...listeners}
                >
                    <HiMenu size={20} />
                </span>
            )}
            {children}
        </div>
    )
}

export const List = ({ defaultValue = [], onChange, readonly = false, disabled = false }) => {
    const [lists, setLists] = useState(
        (Array.isArray(defaultValue) ? defaultValue : []).map((value) => ({
            id: uuidv4(),
            value: value.value,
        }))
    )

    const sensors = useSensors(
        useSensor(PointerSensor, {
            activationConstraint: { distance: 5 },
        })
    )

    // リスト追加
    const addList = () => {
        const newLists = [...lists, { id: uuidv4(), value: '' }]
        setLists(newLists)
        onChange && onChange(newLists)
    }

    // リスト削除
    const removeList = (index) => {
        const newLists = lists.filter((_, i) => i !== index)
        setLists(newLists)
        onChange && onChange(newLists)
    }

    // リスト編集
    const updateList = (index, key, val) => {
        const newLists = lists.map((list, i) => (i === index ? { ...list, [key]: val } : list))
        setLists(newLists)
        onChange && onChange(newLists)
    }

    const ids = lists.map((c) => c.id)

    const handleDragEnd = (event) => {
        const { active, over } = event
        if (!over || active.id === over.id) return
        const oldIndex = lists.findIndex((c) => c.id === active.id)
        const newIndex = lists.findIndex((c) => c.id === over.id)
        const updated = arrayMove(lists, oldIndex, newIndex)
        setLists(updated)
        onChange && onChange(updated)
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
                    <div>
                        {lists.map((list, idx) => (
                            <SortableRow key={list.id} id={list.id} readonly={readonly || disabled}>
                                <TextInput
                                    defaultValue={list.value}
                                    placeholder="リストを入力してください"
                                    onChange={(value, e) => updateList(idx, 'value', value)}
                                    className="w-2/4"
                                    readOnly={readonly || disabled}
                                    disabled={readonly || disabled}
                                />
                                {!readonly && !disabled && (
                                    <Button
                                        size="sm"
                                        color="red"
                                        outline
                                        onClick={() => removeList(idx)}
                                    >
                                        削除
                                    </Button>
                                )}
                            </SortableRow>
                        ))}
                        {!readonly && !disabled && (
                            <Button size="xs" outline onClick={addList}>
                                ＋リストを追加
                            </Button>
                        )}
                    </div>
                </SortableContext>
            </DndContext>
        </>
    )
}
