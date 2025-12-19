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

const Choices = ({ defaultValue = [], onChange, readonly = false, disabled = false }) => {
    const [choices, setChoices] = useState(
        defaultValue.map((v) => ({ ...v, id: v.id || uuidv4() }))
    )

    const sensors = useSensors(
        useSensor(PointerSensor, {
            activationConstraint: { distance: 5 },
        })
    )

    // 選択肢追加
    const addChoice = () => {
        const newChoices = [...choices, { id: uuidv4(), label: '', value: '' }]
        setChoices(newChoices)
        onChange && onChange(newChoices)
    }

    // 選択肢削除
    const removeChoice = (index) => {
        const newChoices = choices.filter((_, i) => i !== index)
        setChoices(newChoices)
        onChange && onChange(newChoices)
    }

    // 選択肢編集
    const updateChoice = (index, key, val) => {
        const newChoices = choices.map((choice, i) =>
            i === index ? { ...choice, [key]: val } : choice
        )
        setChoices(newChoices)
        onChange && onChange(newChoices)
    }

    const ids = choices.map((c) => c.id)

    const handleDragEnd = (event) => {
        const { active, over } = event
        if (!over || active.id === over.id) return
        const oldIndex = choices.findIndex((c) => c.id === active.id)
        const newIndex = choices.findIndex((c) => c.id === over.id)
        const updated = arrayMove(choices, oldIndex, newIndex)
        setChoices(updated)
        onChange && onChange(updated)
    }

    return (
        <DndContext
            sensors={sensors}
            collisionDetection={closestCenter}
            onDragEnd={handleDragEnd}
            modifiers={[restrictToVerticalAxis]}
        >
            <SortableContext items={ids} strategy={verticalListSortingStrategy}>
                <div>
                    {choices.map((choice, idx) => (
                        <SortableRow key={choice.id} id={choice.id} readonly={readonly || disabled}>
                            <TextInput
                                defaultValue={choice.value}
                                placeholder="値"
                                onChange={(value, e) => updateChoice(idx, 'value', value)}
                                className="w-1/12"
                                readOnly={readonly || disabled}
                                disabled={readonly || disabled}
                            />
                            <TextInput
                                defaultValue={choice.label}
                                placeholder="ラベル"
                                onChange={(value, e) => updateChoice(idx, 'label', value)}
                                className="w-1/4"
                                readOnly={readonly || disabled}
                                disabled={readonly || disabled}
                            />
                            {!readonly && !disabled && (
                                <Button
                                    size="sm"
                                    color="red"
                                    outline
                                    onClick={() => removeChoice(idx)}
                                >
                                    削除
                                </Button>
                            )}
                        </SortableRow>
                    ))}
                    {!readonly && !disabled && (
                        <Button size="sm" outline onClick={addChoice}>
                            ＋選択肢を追加
                        </Button>
                    )}
                </div>
            </SortableContext>
        </DndContext>
    )
}

export default Choices
