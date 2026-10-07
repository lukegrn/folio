import { expect, test } from "bun:test";
import { getErrorMessage } from "../errors";

test("Returns the error message for an Error", () => {
  const e = new Error("test message");

  expect(getErrorMessage(e)).toBe("test message");
});

test("Returns a string for anything else", () => {
  const i = 10;

  expect(getErrorMessage(i)).toBe("10");
});
