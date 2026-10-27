import assert from 'node:assert/strict'
import { readFileSync } from 'node:fs'
import { runInNewContext } from 'node:vm'
const binding = JSON.parse(readFileSync(0, 'utf8'))
const point = runInNewContext(`(${binding.expression})`, {
    $wire: { $entangle: (path) => binding.state[path] },
})
assert.equal(point.x, 25.5)
assert.equal(point.y, 70)
for (const width of [200, 480, 900]) {
    point.$refs = {
        surface: {
            getBoundingClientRect: () => ({
                left: 10,
                top: 20,
                width,
                height: width / 2,
            }),
        },
    }
    point.place({ clientX: 10 + width * 0.25, clientY: 20 + (width / 2) * 0.8 })
    assert.equal(point.x, 25)
    assert.equal(point.y, 80)
}
let prevented = false
point.move({
    key: 'ArrowRight',
    shiftKey: false,
    preventDefault: () => (prevented = true),
})
assert.equal(point.x, 26)
assert.equal(point.y, 80)
assert.equal(prevented, true)
point.move({ key: 'ArrowUp', shiftKey: true, preventDefault() {} })
assert.equal(point.y, 70)
point.x = 99
point.move({ key: 'ArrowRight', shiftKey: true, preventDefault() {} })
assert.equal(point.x, 100)
point.y = 0
point.move({ key: 'ArrowUp', shiftKey: false, preventDefault() {} })
assert.equal(point.y, 0)
